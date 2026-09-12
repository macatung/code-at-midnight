# -*- coding: utf-8 -*-
"""
phonetic_normalizer.py
Quy chuẩn & Công cụ Chuẩn Hóa Ngữ Âm Cho AI TTS (Vbee Thanh Long / Anh Khôi)
Chuyển hóa các từ viết tắt tiếng Anh, thương hiệu mạng xã hội và thuật ngữ tiếng Pali
thành chuỗi ký tự ngữ âm thuần Việt, chống vấp váp, đọc sai hoặc nuốt chữ.
"""

import re
from typing import Dict, List, Tuple

# Bảng đối soát phiên âm chuẩn mực (Phonetic Dictionary)
PHONETIC_DICTIONARY: Dict[str, str] = {
    # 1. Từ viết tắt Khoa học Thần kinh & Y học
    r"\bfMRI\b": "ép-em-rờ-ai",
    r"\bmPFC\b": "em-pi-ép-xi",
    r"\bdlPFC\b": "đê-eo-pi-ép-xi",
    r"\bDMN\b": "đê-em-en",
    r"\bADHD\b": "ây-đi-ếch-đi",
    r"\bEEG\b": "e-e-gờ",
    r"\bPET\b": "pét",
    r"\bDNA\b": "đê-en-a",
    r"\bIQ\b": "ai-khiu",
    r"\bEQ\b": "i-khiu",
    r"\bKPI\b": "kê-pi-ai",
    r"\bKPIs\b": "kê-pi-ai",
    r"\bOKRs\b": "ô-ka-rờ",

    # 2. Mạng xã hội, Công nghệ & Từ vựng tiếng Anh thông dụng
    r"\bLinkedIn\b": "Linh-tin",
    r"\bLinkedin\b": "Linh-tin",
    r"\bInstagram\b": "In-sta-gram",
    r"\bFacebook\b": "Phây-s-búc",
    r"\bTikTok\b": "Tích-tốc",
    r"\bYoutube\b": "Du-túp",
    r"\bYouTube\b": "Du-túp",
    r"\bShorts\b": "Sọt-s",
    r"\bReels\b": "Riu-s",
    r"\bflex\b": "phơ-lếch",
    r"\bflexing\b": "phơ-lếch-xinh",
    r"\bfilter\b": "phin-tơ",
    r"\bfilters\b": "phin-tơ",
    r"\bhighlight\b": "hai-lai",
    r"\bhighlights\b": "hai-lai",
    r"\bdeadline\b": "đét-lai",
    r"\bdeadlines\b": "đét-lai",
    r"\bfeedback\b": "phít-bách",
    r"\btoxic\b": "tốc-xích",
    r"\bburnout\b": "bơn-ao",
    r"\bFOMO\b": "phô-mô",
    r"\bhack\b": "hách",
    r"\bhacking\b": "hách-kinh",
    r"\bCarleton\b": "Cát-lơn-tơn",
    r"\bStanford\b": "X-tan-phợt",
    r"\bDopamine\b": "đô-pa-min",
    r"\bdopamine\b": "đô-pa-min",
    r"\bCortisol\b": "coóc-ti-zôn",
    r"\bcortisol\b": "coóc-ti-zôn",
    r"\bAmygdala\b": "a-míc-đa-la",
    r"\bamygdala\b": "a-míc-đa-la",
    r"\bPrefrontal\b": "pờ-ri-phrân-tồ",
    r"\bImposter Syndrome\b": "hội chứng kẻ mạo danh",
    r"\bimposter syndrome\b": "hội chứng kẻ mạo danh",
    r"\bImposter\b": "kẻ mạo danh",
    r"\bimposter\b": "kẻ mạo danh",
    r"\bDunning-Kruger\b": "Đăn-ninh Cờ-ru-gơ",

    # 3. Thuật ngữ Phật học Pali & Sanskrit
    r"\bChanna\b": "Xan-na",
    r"\bMāna\b": "Ma-na",
    r"\bAtimāna\b": "A-ti-ma-na",
    r"\bHīnamāna\b": "Hi-na-ma-na",
    r"\bMuditā\b": "Mu-đi-ta",
    r"\bAtta-dīpa\b": "Át-ta đi-pa",
    r"\bAnattā\b": "A-nát-ta",
    r"\bAnatta\b": "A-nát-ta",
    r"\bAbhidhamma\b": "A-bhi-đham-ma",
    r"\bBrahma-daṇḍa\b": "Bờ-ram-ma đan-đa",
    r"\bBrahmadaṇḍa\b": "Bờ-ram-ma đan-đa",
    r"\bUpekkhā\b": "U-pếch-kha",
    r"\bUpekkha\b": "U-pếch-kha",
    r"\bLokadhamma\b": "Lô-ka-đham-ma",
    r"\bAṭṭha Lokadhammā\b": "Át-tha Lô-ka-đham-ma",
    r"\bSati\b": "Sa-ti",
    r"\bSamādhi\b": "Sa-ma-đi",
    r"\bVīriya\b": "Vi-ri-a",
    r"\bVirya\b": "Vi-ri-a",
    r"\bThīna-middha\b": "Thi-na mít-đa",
    r"\bThīna\b": "Thi-na",
    r"\bMiddha\b": "Mít-đa",
    r"\bTaṇhā\b": "Tan-ha",
    r"\bTanha\b": "Tan-ha",
    r"\bNīvaraṇa\b": "Ni-va-ra-na",
    r"\bNivarana\b": "Ni-va-ra-na",
    r"\bUddhacca\b": "Út-đhát-sa",
    r"\bKukkucca\b": "Cúc-cúc-sa",
    r"\bPapañca\b": "Pa-pan-sa",
    r"\bPassaddhi\b": "Pát-sát-đi",
    r"\bSantuṭṭhi\b": "San-tút-thi",
    r"\bPaṭiccasamuppāda\b": "Pa-tít-sa-sa-múp-pa-đa",
    r"\bAyoniso manasikāra\b": "A-dô-ni-xô ma-na-xi-ka-ra",
    r"\bSammā-Vāyāma\b": "Sam-ma va-da-ma",
    r"\bSammā Vāyāma\b": "Sam-ma va-da-ma",
    r"\bTheravāda\b": "Thê-ra-va-đa",
    r"\bTheravada\b": "Thê-ra-va-đa",
    r"\bKisa Gotamī\b": "Ki-sa Gô-ta-mi",
    r"\bGotamī\b": "Gô-ta-mi",
    r"\bSāriputta\b": "Xa-ri-pút-ta",
    r"\bMoggallāna\b": "Mốc-gơ-la-na",
    r"\bĀnanda\b": "A-nan-đa",
    r"\bAnanda\b": "A-nan-đa",

    # 4. Chống lỗi đọc chữ cái ALL-CAPS (Invariant 8)
    r"\bCỦA TA\b": "Của Ta",
    r"\bCÁI TA\b": "Cái Ta",
    r"\bBẢN NGÃ CỦA TA\b": "Bản Ngã Của Ta",
    r"\bNGÃ\b": "Ngã",
}

class PhoneticNormalizer:
    """Chuẩn hóa văn bản kịch bản sang chuỗi ngữ âm thuần Việt cho TTS"""

    @classmethod
    def normalize_to_spoken(cls, text: str) -> str:
        """Chuyển đổi chuỗi văn bản học thuật sang chuỗi ngữ âm cho TTS"""
        if not text:
            return ""

        result = text
        for pattern, replacement in PHONETIC_DICTIONARY.items():
            result = re.sub(pattern, replacement, result)

        # Xóa các ký tự ngoặc đơn nếu có dạng "fMRI (ép-em-rờ-ai)" -> lấy phần phiên âm
        result = re.sub(r"\(([^)]+)\)", r", \1 ,", result)

        # Chuẩn hóa khoảng trắng thừa và dấu câu
        result = re.sub(r"\s+", " ", result)
        result = re.sub(r"\s*,\s*,", ",", result)
        result = re.sub(r"\s*,\s*", ", ", result)
        result = re.sub(r"\s*\.\s*", ". ", result)

        return result.strip()

    @classmethod
    def split_dual_track(cls, scene: dict) -> Tuple[str, str]:
        """
        Đảm bảo mỗi scene luôn có đầy đủ:
        - display_text: Chữ hiển thị chuẩn học thuật (cho subtitle ASS & bài viết)
        - spoken_text: Chữ phiên âm ngữ âm thuần Việt (cho engine Vbee TTS)
        """
        display_text = scene.get("display_text") or scene.get("text", "")
        spoken_text = scene.get("spoken_text") or cls.normalize_to_spoken(display_text)
        return display_text, spoken_text

if __name__ == "__main__":
    sample_text = (
        "Khi đưa một người mắc Imposter Syndrome vào máy chụp fMRI, các nhà khoa học "
        "thấy vùng mPFC và DMN phát tín hiệu căng thẳng. "
        "Mỗi tối khi lướt LinkedIn hay Instagram thấy bạn bè flexing, tâm Ngã Mạn Māna trỗi dậy. "
        "Trong Abhidhamma, Đức Phật dạy về điển tích Tỳ-kheo Channa và án phạt Brahma-daṇḍa, "
        "chỉ ra con đường thoát ly bằng Muditā và Atta-dīpa."
    )
    spoken = PhoneticNormalizer.normalize_to_spoken(sample_text)
    print("--- VĂN BẢN HIỂN THỊ (DISPLAY TEXT) ---")
    print(sample_text)
    print("\n--- VĂN BẢN ĐỌC TTS (SPOKEN TEXT) ---")
    print(spoken)
