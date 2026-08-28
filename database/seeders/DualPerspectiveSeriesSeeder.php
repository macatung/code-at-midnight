<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use Carbon\Carbon;

class DualPerspectiveSeriesSeeder extends Seeder
{
    /**
     * Run the database seeds for Dual Perspective Series.
     * Perspective 1 (macatung.dev): Góc nhìn Đời Sống & Tâm Lý Xã Hội Hiện Đại (Chia sẻ đời thường, chân thực)
     * Perspective 2 (theravada.macatung.dev): Góc nhìn Thiền Quán & Phật Giáo Nguyên Thủy (Chiêm nghiệm tu tập, Tứ Diệu Đế, Vô Ngã)
     */
    public function run(): void
    {
        $now = Carbon::now();

        // =========================================================================
        // PAIR 1: FOMO & CƠN NGHIỆN MÀN HÌNH
        // =========================================================================
        $life1Content = <<< 'MARKDOWN'
Có bao giờ bạn giật mình nhận ra, điều đầu tiên bạn làm khi vừa mở mắt vào buổi sáng không phải là ngắm nhìn ánh nắng đầu ngày hay uống một ngụm nước, mà là quờ quạng tay tìm chiếc điện thoại?

Chúng ta sống trong một thời đại mà chiếc màn hình phát sáng dường như đã trở thành một phần thân thể nối dài. Chỉ cần vài phút đứng chờ đèn đỏ, ngồi đợi thang máy hay trong một khoảng lặng giữa cuộc trò chuyện, ngón tay ta lại tự động trượt mở màn hình một cách vô thức. Đằng sau thói quen ấy là một nỗi bất an mơ hồ nhưng dai dẳng: **Nỗi sợ bị bỏ rơi (FOMO — Fear Of Missing Out)**.

### 1. Cơn Đói Cảm Xúc Trong Thế Giới Đầy Ắp Tin Tức

Khi lướt qua hàng trăm bức ảnh du lịch lộng lẫy, những bài đăng thành công rực rỡ hay các dòng tin nóng sốt, ta ngỡ rằng mình đang kết nối với thế giới. Nhưng kỳ lạ thay, càng lướt lâu, lòng ta lại càng trống trải. 

Mỗi thông báo mới, mỗi lượt like hay một video ngắn giật gân chỉ mang lại một tia phấn khích ngắn ngủi kéo dài vài giây. Ngay sau đó là cảm giác hụt hẫng và thôi thúc ngón tay phải tiếp tục cuộn xuống để tìm kiếm điều gì đó mới mẻ hơn. Ta như người đang đói ăn phải những hạt cát lấp lánh: Càng nhai càng rát miệng, nhưng không thể dừng lại vì sợ ngoài kia đang có một bữa tiệc mà mình vắng mặt.

:::perspective
[life]
#### Góc Nhìn Đời Thường: Khoảng Trống Cô Đơn Đằng Sau Màn Hình
Chúng ta cầm điện thoại lên không phải vì có việc khẩn cấp, mà phần lớn là để trốn chạy cảm giác cô đơn và trống rỗng của chính mình. Ta sợ đối diện với sự tĩnh lặng. Ta sợ ở một mình với những suy nghĩ chưa được giải quyết trong lòng, nên vội vàng tìm kiếm âm thanh và hình ảnh trên mạng để lấp đầy tâm trí. Nhưng sự ồn ào ấy chỉ che đậy nỗi cô đơn chứ chưa từng xoa dịu được nó.
[/dev]
[theravada]
#### Quán Chiếu Thiền Quán: Cơn Khát Vô Tận Của Ái Dục (Taṇhā)
Dưới tuệ giác Phật giáo, hiện tượng này chính là sự vận hành của **Thập Nhị Duyên Khởi**: Mắt và tai tiếp xúc với hình ảnh số sinh ra **Xúc (Phassa)**, từ đó phát sinh **Cảm Thọ (Vedanā)** thích thú tạm bợ. Cảm thọ ấy lập tức nuôi dưỡng **Ái Dục (Taṇhā)** — cơn khát khao muốn có thêm cảm giác dễ chịu. Đức Phật ví ái dục như người uống nước muối: Càng uống lại càng thấy khát, càng đắm chìm vào kích thích giác quan thì tâm thức lại càng bồn chồn, bất an.
[/theravada]
:::

### 2. Tìm Lại Sự Bình Yên Trong Sự Bỏ Lỡ (JOMO)

Để tìm lại sự tự do, điều chúng ta cần không phải là đập bỏ chiếc điện thoại hay trốn lên núi cao, mà là lòng can đảm để **chấp nhận bỏ lỡ (JOMO — Joy Of Missing Out)**:

- Cho phép bản thân không cần biết hết mọi trào lưu trên mạng.
- Tắt bớt những thông báo vô bổ để lắng nghe tiếng thở của chính mình.
- Dành trọn vẹn sự hiện diện cho bữa cơm gia đình hay một buổi chiều tản bộ không mang theo thiết bị.

:::perspective
[life]
#### Trải Nghiệm Đời Thường: Học Cách Tắt Nguồn Cho Tâm Trí
Khi bạn dám tắt chuông điện thoại trong một buổi tối và ngồi đọc một cuốn sách hay trò chuyện chân thành với người thân, bạn sẽ nhận ra thế giới ngoài kia chẳng hề sụp đổ nếu bạn vắng mặt vài giờ. Những điều quý giá nhất của cuộc sống luôn diễn ra ở thế giới thực, ngay trước mắt bạn.
[/dev]
[theravada]
#### Pháp Hành Chánh Niệm: Hộ Trì Các Căn (Indriyasaṃvara)
Khi tay chuẩn bị bấm vào điện thoại, hãy dừng lại trong 3 nhịp thở. Nhận biết rõ ràng: *"Tâm bồn chồn đang có mặt"*. Chỉ quan sát cảm giác thèm muốn ấy sinh lên rồi tự tan biến mà không đồng hóa mình với nó. Đó chính là sự tự tại đầu tiên của người thực tập tỉnh giác giữa đời thường.
[/theravada]
:::
MARKDOWN;

        $theravada1Content = <<< 'MARKDOWN'
Thế giới hiện đại với hàng tỷ thiết bị kết nối mạng đang giăng ra một mạng lưới kích thích giác quan vô tiền khoáng hậu. Người trẻ ngày nay luôn sống trong một nỗi âu lo thường trực mang tên **FOMO (Nỗi sợ bị bỏ lỡ)**. Chỉ cần vài phút không nhìn vào màn hình, tâm thức liền khởi lên cảm giác bức bối, hoang mang như thể mình vừa bị rớt lại phía sau dòng chảy của nhân loại.

Dưới lăng kính của Giáo Pháp Nguyên Thủy (Theravāda Dhamma), hiện tượng này không có gì mới mẻ: Đó chính là bản chất muôn đời của **Ái Dục (Taṇhā)** và **Chấp Thủ (Upādāna)** trong vòng luân hồi của Danh Sắc.

> *“Manopubbaṅgamā dhammā, manoseṭṭhā manomayā…”*  
> Ý dẫn đầu các pháp, Ý làm chủ, ý tạo.  
> — Kinh Pháp Cú (Dhammapada), Kệ số 1

### 1. Cơn Khát Vô Tận Của Ngũ Uẩn

Trong *Kinh Tương Ưng Bộ (Saṃyutta Nikāya)*, Đức Thế Tôn ví Ái dục như người khát nước uống nước biển: Càng uống lại càng thấy khát, càng nạp thêm tin tức số lại càng cảm thấy trống rỗng và thèm khát kích thích mãnh liệt hơn.

Khi một thông báo hiện lên trên điện thoại, **Sắc căn** (mắt) tiếp xúc với **Sắc trần** (ánh sáng màn hình), sinh khởi **Nhãn thức**. Ngay khoảnh khắc đó, tâm cảm nhận một **Cảm thọ (Vedanā)**. Nếu cảm thọ đó là dễ chịu, tâm liền bị cuốn vào **Tham ái (Lobha)**; nếu không có tin tức gì mới, tâm lại rơi vào cảm thọ bực bội, khó chịu của **Sân tâm (Dosa)** hoặc mê mờ của **Si phần (Moha)**.

:::perspective
[theravada]
#### Quán Chiếu: Thấu Suốt Bản Chất Mong Manh Của Cảm Thọ
Cảm giác thỏa mãn khi đọc được một thông báo hay lượt tương tác chỉ tồn tại trong vài sát-na tâm ngắn ngủi rồi biến mất. Vì không thấy được tính **Vô thường (Anicca)** của cảm thọ đó, tâm thức liền mù quáng đòi hỏi một kích thích mới để duy trì ảo ảnh hạnh phúc. Đây chính là gốc rễ của sự nghiện ngập giác quan trong thời đại số.
[/theravada]
[life]
#### Lăng Kính Đời Sống: Sự Mất Kết Nối Với Thực Tại
Người nghiện màn hình thường nghĩ mình đang mở rộng thế giới, nhưng thực chất họ đang đánh mất kết nối với chính cơ thể và những người thân yêu bên cạnh. Họ nhìn thấy mọi thứ trên mạng, nhưng không còn cảm nhận được hương vị của một tách trà hay ánh mắt của người ngồi đối diện.
[/dev]
:::

### 2. Phương Thuốc Chánh Niệm: Quán Cảm Thọ (Vedanānupassanā)

Làm thế nào để người tu học và người sống trong đời sống hiện đại thoát khỏi cái bẫy vô tận này? Câu trả lời nằm ở pháp môn **Tứ Niệm Xứ (Satipaṭṭhāna)**.

Mỗi khi tay bạn chuẩn bị với lấy chiếc điện thoại theo phản xạ tự động:
1. **Dừng lại 3 giây**: Nhận biết một cách tỉnh thức: *"Tâm tham đang khởi lên, cảm giác bồn chồn đang có mặt"*.
2. **Quán sát cảm thọ trên thân**: Cảm nhận nhịp tim, sự căng thẳng ở cơ mặt hay cảm giác nôn nóng nơi lồng ngực.
3. **Thấy rõ tính sinh - diệt**: Cảm giác thèm muốn ấy chỉ là một pháp hữu vi (Saṅkhāra), do duyên sinh ra và chắc chắn sẽ tự tan biến khi duyên hết.

:::perspective
[theravada]
#### Pháp Hành: Tự Do Trong Từng Khoảnh Khắc Dừng Lại
Khi bạn có thể đứng nhìn cơn thèm muốn lướt mạng trôi qua mà không bị nó lôi kéo hành động, bạn đã đạt được một bước tự do chân thực. Tâm thức không còn là nô lệ của những kích thích bên ngoài.
[/theravada]
[life]
#### Góc Nhìn Đời Thường: Tìm Lại Sự Tĩnh Lặng Cần Thiết
Sự bình yên không đến từ việc chúng ta biết thêm bao nhiêu tin tức giật gân, mà đến từ khả năng an trú trong sự tĩnh lặng của hiện tại mà không cảm thấy sợ hãi hay thiếu thốn.
[/dev]
:::
MARKDOWN;

        // =========================================================================
        // PAIR 2: ÁP LỰC THÀNH CÔNG & GUỒNG QUAY KIỆT SỨC
        // =========================================================================
        $life2Content = <<< 'MARKDOWN'
Trong xã hội ngày nay, dường như có một cuộc đua ngầm đang diễn ra khắp mọi nơi: *Phải mua nhà trước 30 tuổi, phải có danh vị xã hội, phải làm việc 14 tiếng mỗi ngày để không bị xem là kẻ thụt lùi*. Người ta khoe nhau số giờ làm việc xuyên đêm, khoe những bữa ăn vội vàng trong phòng họp như một bằng chứng kiêu hãnh của sự nỗ lực.

Thế nhưng, sau những chuỗi ngày gồng mình ấy, điều gì còn lại? Là những cơn đau dạ dày mãn tính, những đêm mất ngủ trằn trọc, và một cảm giác kiệt quệ tận cùng từ sâu trong tâm hồn — trạng thái mà chúng ta gọi là **Burnout**.

### 1. Cảm Giác Tội Lỗi Khi Dám Nghỉ Ngơi

Điều đáng sợ nhất của lối sống này không chỉ là sự kiệt sức về thể xác, mà là **cảm giác tội lỗi** bị gieo rắc vào tâm trí: Chỉ cần cho phép mình nằm nghỉ một buổi chiều hay từ chối một công việc, ta liền cảm thấy cắn rứt, tự trách mình là kẻ lười biếng, vô dụng.

Ta ép cơ thể làm việc như một cỗ máy không cần bảo dưỡng. Ta uống cà phê để gượng dậy vào buổi sáng, rồi lại cần thuốc an thần để chợp mắt vào ban đêm. Ta tưởng rằng mình đang nỗ lực vì một tương lai hạnh phúc, nhưng thực chất ta đang hy sinh chính hạnh phúc và sức khỏe của ngày hôm nay cho một ảo ảnh chưa bao giờ tới.

:::perspective
[life]
#### Góc Nhìn Đời Thường: Cái Giá Của Sự "Bận Rộn Ảo Tưởng"
Nhiều người trong chúng ta biến sự bận rộn thành một tấm lá chắn để trốn tránh việc đối diện với ý nghĩa thực sự của cuộc sống. Ta bận rộn để cảm thấy mình quan trọng, bận rộn để không phải tự hỏi: *"Nếu buông bỏ những danh vọng này, mình thực sự là ai và mình muốn gì?"*.
[/dev]
[theravada]
#### Quán Chiếu Thiền Quán: Sự Thiêu Đốt Của Ngọn Lửa Tham Ái
Dưới góc nhìn Phật giáo, sự cố gắng quá sức trong căng thẳng không phải là Chánh Tinh Tấn. Đó là tâm **Tham (Lobha)** muốn nắm bắt kết quả nhanh chóng kết hợp với **Ngã Mạn (Māna)** sợ thua kém người đời. Lòng tham ấy biến sự nỗ lực thành ngọn lửa thiêu đốt thân tâm, làm khởi sinh **Trạo Cử (Uddhacca)** — sự lao chao bất an, để rồi kết thúc bằng sự kiệt quệ và thối chí.
[/theravada]
:::

### 2. Học Lại Bài Học Về Cây Đàn Của Cuộc Đời

Một nhạc công tài ba hiểu rằng: Cây đàn chỉ tấu nên khúc nhạc du dương khi dây đàn được căng **vừa vặn**. Nếu vặn dây quá căng, dây sẽ đứt ngay khi vừa gảy; nếu để dây quá chùng, đàn sẽ không thể phát ra âm thanh.

Cuộc sống của mỗi con người cũng vậy:
- Nỗ lực hết mình trong công việc là điều đáng quý, nhưng biết dừng lại để nghỉ ngơi và tái tạo năng lượng là một trí tuệ sâu sắc.
- Học cách nói lời từ chối với những gánh nặng không thuộc về mình.
- Hiểu rằng sức khỏe và sự an yên trong tâm hồn là vốn liếng quý giá nhất của đời người.

:::perspective
[life]
#### Lời Khuyên Đời Sống: Cuộc Đời Là Cuộc Chạy Marathon, Không Phải Nước Rút 100m
Chạy thấu đêm để rồi gục ngã suốt nhiều tháng sẽ khiến bạn đánh mất nhiều hơn là được. Hãy bước đi bền bỉ, trân trọng từng ngày và cho phép bản thân được sống như một con người bình thường, biết mệt thì nghỉ, biết đủ thì vui.
[/dev]
[theravada]
#### Trí Tuệ Trung Đạo: Sống Biết Đủ (Santuṭṭhi)
Hài lòng với những điều kiện hiện tại sau khi đã làm hết trách nhiệm chân chánh. Khi tâm buông bỏ được lòng tham cầu kết quả, bạn sẽ làm việc với một tâm thái thanh thản, nhẹ nhàng và tràn đầy năng lượng bền bỉ.
[/theravada]
:::
MARKDOWN;

        $theravada2Content = <<< 'MARKDOWN'
Trong các giảng đường hiện đại và đời sống xã hội, người ta không ngừng rao giảng về tinh thần chiến đấu không ngừng nghỉ: *Làm việc cật lực, tối đa hóa từng phút giây để vươn tới đỉnh cao*. Thế nhưng đằng sau ánh hào quang ấy là hàng triệu tâm hồn kiệt quệ, trầm cảm và trống rỗng — một trạng thái mà tâm lý học gọi là **Burnout**.

Cách đây hơn 2.500 năm, trong kinh điển Pāḷi, Đức Phật đã từng khai thị cho một vị đệ tử tên là **Soṇa Koḷivisa** — người xuất thân quyền quý nhưng khi đi tu đã thực hành khổ hạnh quá mức, đi kinh hành đến mức rách cả hai bàn chân tóe máu mà vẫn không đắc quả, dẫn đến tâm trạng thối chí muốn hoàn tục.

> *“Này Soṇa, khi xưa ở tại gia, con có biết gảy đàn tỳ-bà không? Khi các dây đàn quá căng, đàn có phát ra âm thanh hòa nhã không? Khi các dây đàn quá chùng, đàn có phát ra âm thanh hòa nhã không? Chỉ khi dây đàn được lên vừa vặn, không quá căng, không quá chùng, đàn mới phát ra khúc nhạc êm dịu…”*  
> — Tăng Chi Bộ Kinh (Aṅguttara Nikāya), Kinh Soṇa (AN 6.55)

### 1. Bản Chất Tâm Học Của Căn Bệnh Kiệt Quệ

Dưới góc nhìn Vi Diệu Pháp (Abhidhamma), sự nỗ lực làm việc quá sức trong trạng thái căng thẳng không phải là **Chánh Tinh Tấn (Sammā Vāyāma)**. Đó là sự tinh tấn bị thúc đẩy bởi **Tham (Lobha)** và **Ngã Mạn (Māna)**: Tham muốn đạt được tiền tài, danh vị, sợ hãi bị thua kém bạn bè.

Khi tâm bị thiêu đốt bởi ngọn lửa tham ái, nó không thể có sự an tịnh. Càng cố gắng, tâm lại càng rơi vào **Trạo Cử (Uddhacca)** — trạng thái dao động, bất an. Khi nguồn năng lượng sinh học cạn kiệt, tâm thức lập tức rơi vào trạng thái cực đoan đối nghịch: **Hôn Trầm Thụy Miên (Thīna-Middha)** — sự lười biếng, chán chường, buông xuôi và mất hết phương hướng.

:::perspective
[theravada]
#### Quán Chiếu: Sự Cân Bằng Giữa Năm Quyền (Indriyasamatta)
Trong lộ trình tu tập, Đức Phật luôn nhấn mạnh sự cân bằng giữa **Tín (Saddhā)** với **Tuệ (Paññā)**, và giữa **Tinh Tấn (Vīriya)** với **Định (Samādhi)**. Nếu Tinh tấn quá nhiều mà thiếu Định, tâm sẽ lao chao tán loạn; nếu Định quá nhiều mà thiếu Tinh tấn, tâm sẽ rơi vào hôn mê thụ động. Chánh Niệm (**Sati**) là yếu tố đóng vai trò giữ gìn sự cân bằng hoàn hảo giữa các năng lực này.
[/theravada]
[life]
#### Góc Nhìn Đời Thường: Cân Bằng Cuộc Sống Không Phải Sự Yếu Đuối
Biết lắng nghe giới hạn của cơ thể và dành thời gian chăm sóc sức khỏe tinh thần không phải là biểu hiện của sự buông xuôi, mà là điều kiện tiên quyết để sống một cuộc đời có ý nghĩa lâu dài.
[/dev]
:::

### 2. Sống Với Tinh Thần Biết Đủ (Santuṭṭhi)

Chữa lành sự kiệt quệ không có nghĩa là trốn tránh trách nhiệm, mà là chuyển hóa động lực hành động từ **Tham Ái** sang **Tuệ Giác** và **Tâm Từ (Mettā)**:

1. **Biết Đủ (Santuṭṭhi)**: Hài lòng với những gì mình đang có sau khi đã nỗ lực chân chánh, không để tâm bị cuốn theo sự so kè vô vọng của thế gian.
2. **Hiện Tại Lạc Trú (Diṭṭhadhammasukhavihāra)**: Tìm thấy niềm vui trong chính công việc đang làm ở giây phút này, chứ không dời niềm hạnh phúc vào một cột mốc xa vời trong tương lai.

:::perspective
[theravada]
#### Tuệ Giác: Buông Bỏ Sự Đồng Hóa Với Kết Quả
Hành động hết lòng trong Chánh nghiệp nhưng không dính mắc vào kết quả được hay mất. Khi không còn gánh nặng của Cái Tôi kiêu hãnh trên vai, bạn sẽ làm việc với một năng lượng thanh thản và bền bỉ vô biên.
[/theravada]
[life]
#### Lắng Nghe Tiếng Nói Bên Trong
Khi bạn ngừng so sánh bước chân của mình với người khác, bạn sẽ nhận ra con đường của riêng mình luôn ngập tràn vẻ đẹp bình dị mà bấy lâu nay bạn đã vô tình lướt qua.
[/dev]
:::
MARKDOWN;

        // =========================================================================
        // PAIR 3: CĂN BỆNH SO SÁNH & VỎ BỌC HÀO NHOÁNG
        // =========================================================================
        $life3Content = <<< 'MARKDOWN'
Có một nghịch lý phổ biến trong xã hội hiện đại: Chúng ta thường nhìn vào những khoảnh khắc huy hoàng nhất của người khác trên mạng xã hội để rồi tự dằn vặt những lúc tồi tệ nhất của chính mình.

Đi họp lớp, lướt bảng tin, hay trong những cuộc trò chuyện cà phê cuối tuần, câu chuyện dường như luôn xoay quanh việc ai vừa mua xe mới, ai vừa thăng chức, ai có con cái học trường quốc tế. Dù muốn hay không, một cảm giác so sánh ngầm luôn len lỏi xuất hiện: Hoặc là ta cảm thấy tự ti, tủi thân vì thấy mình kém cỏi; hoặc nếu ta nhỉnh hơn một chút, lòng ta lại khởi lên sự tự đắc ngấm ngầm.

### 1. Cái Bẫy Của Vỏ Bọc Hoàn Hảo

Để không cảm thấy bị thua kém, nhiều người bắt đầu khoác lên mình những chiếc mặt nạ lộng lẫy:
- Mua sắm những món đồ hiệu vượt quá khả năng tài chính chỉ để nhận được ánh mắt ngưỡng mộ của người dưng.
- Đăng tải những bức ảnh nụ cười rạng rỡ lên mạng xã hội ngay cả khi trong lòng đang ngập tràn nước mắt và sự bế tắc.
- Giấu kín những tổn thương, vấp ngã vì sợ người khác đánh giá mình là kẻ thất bại.

Chúng ta đang sống cho ánh nhìn của người khác nhiều hơn là sống cho chính cảm nhận chân thật của bản thân. Ta trở thành diễn viên chính trong một vở kịch mà khán giả thực chất cũng chỉ đang bận tâm lo lắng cho vai diễn của riêng họ.

:::perspective
[life]
#### Góc Nhìn Đời Thường: Nỗi Cô Đơn Sau Những Lời Tán Thưởng
Khi giá trị bản thân được xây dựng dựa trên sự công nhận của đám đông, chúng ta sẽ luôn sống trong nỗi sợ hãi bị lãng quên. Lời khen của thiên hạ như những bong bóng xà phòng rực rỡ nhưng dễ vỡ: Nó làm ta bay bổng trong giây lát, nhưng khi tan đi lại để lại sự trống vắng khôn cùng.
[/dev]
[theravada]
#### Quán Chiếu Thiền Quán: Ba Cạm Bẫy Của Ngã Mạn (Māna)
Trong giáo lý Phật giáo, tâm so sánh được gọi là **Mạn (Māna)**:
1. Thấy mình **hơn người** (Seyyamāna) là ngã mạn.
2. Thấy mình **bằng người** (Sadisamāna) là ngã mạn.
3. Thấy mình **thua kém người khác** (Hīnamāna — mặc cảm tự ti) cũng chính là một hình thái của ngã mạn!  
Bởi vì tất cả đều bắt nguồn từ một ảo tưởng cốt lõi: Tin rằng có một "Cái Tôi" riêng biệt đang tồn tại để đem ra so đo với thế gian.
[/theravada]
:::

### 2. Mỗi Bông Hoa Đều Có Mùa Nở Của Riêng Mình

Hạt sồi không cần phải cố gắng lớn nhanh như cây tre, và hoa sen dưới đầm cũng không cần so đo hương thơm với hoa hồng trên cạn.

Khi bạn buông bỏ nhu cầu phải chứng tỏ mình với người khác:
- Bạn học được cách **thành tâm chúc mừng** hạnh phúc của người xung quanh mà không kèm theo sự ghen tị.
- Bạn học được cách **ôm ấp những khuyết điểm** và sự bất toàn của chính mình như một phần chân thực của cuộc sống.
- Bạn tìm thấy sự tự do để sống một cuộc đời chân thật, không son phấn, không giả tạo.

:::perspective
[life]
#### Lời Khuyên Đời Thường: Hãy Là Chính Mình, Một Cách Trọn Vẹn
Người bình an nhất không phải là người có nhiều thứ nhất, mà là người không còn cảm thấy cần phải chứng minh giá trị của mình cho bất kỳ ai thấy.
[/dev]
[theravada]
#### Tuệ Giác Vô Ngã (Anattā): Đứng Vững Trước Tám Ngọn Gió Đời
Khi hiểu rằng danh vọng, lời khen chê đều là những pháp hữu vi vô thường, tâm thức sẽ đạt được sự an nhiên tự tại (**Upekkhā**). Ngọn gió khen ngợi không làm ta tự đắc, và lời chê bai cay nghiệt cũng không thể làm tổn thương sự bình an cốt lõi bên trong.
[/theravada]
:::
MARKDOWN;

        $theravada3Content = <<< 'MARKDOWN'
Trong một xã hội nơi mọi cá nhân đều có thể trưng bày những khoảnh khắc rực rỡ nhất của đời mình lên không gian mạng, con người ngày càng rơi sâu vào một căn bệnh tâm lý trầm kha: **Căn bệnh So Sánh**.

Nhìn thấy người khác khoe tài sản, thăng tiến, cuộc sống nhung lụa... tâm thức con người lập tức nổi sóng: Hoặc là rơi vào mặc cảm tủi hổ vì thấy mình thua kém, hoặc khởi sinh tâm đố kỵ ghen ghét, hoặc nếu bản thân có chút thành tựu thì liền sinh tâm kiêu ngạo, coi thường người khác.

Đức Phật Gotama đã từng dạy rằng, gốc rễ của mọi nỗi khổ đau trong các mối quan hệ xã hội đều bắt nguồn từ hai sợi dây trói buộc sâu dày: **Thân Kiến (Sakkāya-diṭṭhi)** và **Ngã Mạn (Māna)**.

> *“Kẻ ngu si nghĩ rằng: ‘Đây là con tôi, đây là tài sản của tôi’. Tự thân mình còn không có thật, huống chi là con cái hay tài sản?”*  
> — Kinh Pháp Cú (Dhammapada), Kệ số 62

### 1. Ba Hình Thái Vi Tế Của Ngã Mạn (Māna)

Trong Tạng Vi Diệu Pháp, **Mạn (Māna)** là tâm sở bất thiện đặc trưng bởi sự so đo, định vị bản thân với kẻ khác. Hầu hết mọi người đều lầm tưởng rằng chỉ những kẻ hống hách, khoe khoang mới là người có ngã mạn. Nhưng trí tuệ Phật giáo phân tích 3 khía cạnh sâu sắc:

1. **Thắng Mạn (Seyyamāna)**: Tâm tự đắc thấy mình tài giỏi, giàu có, đạo đức hơn người khác.
2. **Đẳng Mạn (Sadisamāna)**: Tâm so sánh thấy mình cũng ngang hàng, không thua kém ai, từ đó sinh tâm cố chấp, không chịu lắng nghe học hỏi.
3. **Ty Mạn (Hīnamāna)**: Mặc cảm tự ti, thấy mình bất tài, xấu xí, nghèo khổ hơn người khác rồi sinh ra tủi phận, oán trách số phận và đố kỵ (**Issā**).

Cả ba trạng thái này thực chất chỉ là hai mặt của cùng một đồng xu: Sự bám chấp điên đảo vào ảo tưởng rằng có một "Cái Tôi" (Self) cần phải được bảo vệ, nâng niu và khẳng định vị thế trước xã hội.

:::perspective
[theravada]
#### Tuệ Giác: Mạng Xã Hội Là Cỗ Máy Nuôi Dưỡng Ngã Kiến
Mỗi nút Like, mỗi lời tán dương thực chất chỉ đang tiếp thêm thức ăn cho con quái vật "Ngã Kiến" trong tâm thức. Khi bạn dựa vào sự công nhận của người ngoài để thấy mình có giá trị, bạn đã trao chìa khóa bình an của cuộc đời mình vào tay người khác.
[/theravada]
[life]
#### Góc Nhìn Đời Thường: Giải Thoát Khỏi Áp Lực Thể Hiện
Khi ta can đảm gỡ bỏ lớp mặt nạ hào nhoáng để sống thật với chính mình, ta không chỉ tìm lại được sự nhẹ nhõm cho bản thân mà còn tạo ra sự chân thành ấm áp cho những người xung quanh.
[/dev]
:::

### 2. Vượt Lên Tám Ngọn Gió Đời (Bát Phong Bất Động)

Đức Thế Tôn dạy rằng, trong cuộc đời này, không một ai — dù là bậc thánh nhân hay người phàm phu — có thể tránh khỏi sự va đập của **Tám Ngọn Gió Đời (Aṭṭha Lokadhammā)**:
- **Lợi** (Được tài vật) ↔ **Suy** (Mất mát tài vật)
- **Danh** (Được khen ngợi, tiếng tốt) ↔ **Hủy** (Bị phỉ báng, mất danh dự)
- **Xưng** (Được tán thán trước mặt) ↔ **Cơ** (Bị chê bai sau lưng)
- **Lạc** (Hưởng an vui) ↔ **Khổ** (Gặp hoạn nạn, khổ đau)

Người có trí tuệ quán chiếu thấy rõ tám ngọn gió này đều là **Vô thường, Biến hoại, Không thể làm chủ được**. Khi tâm an trú trong chánh niệm và tuệ giác **Vô Ngã (Anattā)**, ngọn gió khen ngợi không làm tâm ta phồng lên kiêu ngạo, và lời chê bai cay nghiệt cũng không thể làm tâm ta lung lay sụp đổ.

:::perspective
[theravada]
#### Thực Hành: Xả Tâm (Upekkhā) Trước Thị Phi
Khi đón nhận một lời khen hay tiếng chê, hãy quán sát cảm giác rung rinh của tâm thức. Nhìn nó như một cơn gió thoảng qua mái hiên chùa, không nắm giữ, không đồng hóa. Đó chính là sự an nhiên giữa trần gian.
[/theravada]
[life]
#### Đời Sống Tự Tại
Hiểu rằng miệng lưỡi thế gian luôn đổi thay theo từng cơn gió. Sống ngay thẳng, tử tế và có trách nhiệm với lương tâm của mình chính là chiếc mỏ neo vững chắc nhất cuộc đời.
[/dev]
:::
MARKDOWN;

        // =========================================================================
        // PAIR 4: NỖI BẤT AN TƯƠNG LAI & KHỦNG HOẢNG BẤT ĐỊNH
        // =========================================================================
        $life4Content = <<< 'MARKDOWN'
Chúng ta đang sống trong một thời đại mà từ khóa phổ biến nhất có lẽ là **"Bất định"**.

Kinh tế biến động, công nghệ thay đổi từng ngày, những ngành nghề từng được coi là vững như bàn thạch bỗng chốc trở nên lung lay. Người trẻ bước vào đời với đầy ắp những câu hỏi lo âu: *"5 năm nữa mình sẽ ra sao? Công việc của mình có còn tồn tại không? Làm sao để lập kế hoạch cho một tương lai mà ngày mai ra sao ta còn chưa biết?"*.

Nỗi sợ hãi này đẩy nhiều người vào trạng thái căng thẳng thường trực, mất ngủ vì cố gắng dự đoán và kiểm soát mọi rủi ro có thể xảy ra trong đời.

### 1. Ảo Tưởng Về Sự Kiểm Soát Tuyệt Đối

Tại sao sự bất định lại khiến chúng ta sợ hãi đến vậy? Bởi vì con người có một nhu cầu bản năng: **Muốn mọi thứ phải nằm trong tầm kiểm soát của mình**. 

Ta lập những kế hoạch chi tiết cho 10 năm tới, vạch sẵn từng bậc thang danh vọng, và hy vọng cuộc đời sẽ diễn ra chính xác theo kịch bản ấy. Nhưng thực tế nghiệt ngã là cuộc đời chưa bao giờ ký cam kết với kế hoạch của bất kỳ ai. Một trận đại dịch bất ngờ, một biến cố sức khỏe, hay một bước ngoặt của thời đại có thể xóa sạch mọi dự tính chỉ sau một đêm.

Càng cố gắng kiểm soát những điều không thể kiểm soát, tâm trí ta lại càng hoảng loạn và kiệt sức.

:::perspective
[life]
#### Góc Nhìn Đời Thường: Nỗi Lo Âu Đang Đánh Cắp Giây Phút Hiện Tại
Phần lớn những điều tồi tệ khiến chúng ta mất ngủ đêm nay thực chất chưa từng xảy ra trong thực tế; chúng chỉ là những kịch bản đáng sợ do trí tưởng tượng của ta vẽ nên. Bằng việc lo sợ cho tương lai, ta đang đánh mất đi khả năng tận hưởng và giải quyết tốt nhất những gì đang có ở hiện tại.
[/dev]
[theravada]
#### Quán Chiếu Thiền Quán: Thấu Suốt Quy Luật Vô Thường (Anicca)
Dưới tuệ giác Phật giáo, nỗi sợ hãi trước sự bất định bắt nguồn từ **Thường Kiến (Sassata-diṭṭhi)** — ảo tưởng mong muốn vạn vật phải ổn định, bất biến theo ý mình. Nhưng chân lý của vũ trụ là **Vô Thường**: Tất cả mọi hiện tượng hữu vi (Saṅkhāra) đều sinh diệt không ngừng trong từng sát-na. Chấp nhận sự đổi thay như một lẽ tự nhiên chính là bước đầu tiên để tâm thức thoát khỏi sự sợ hãi.
[/theravada]
:::

### 2. Học Cách Lướt Trên Những Ngọn Sóng Thay Đổi

Bạn không thể ngăn những con sóng ập vào bờ, nhưng bạn có thể **học cách lướt sóng**.

Đối diện với một thế giới luôn biến động:
- **Tập trung vào những gì nằm trong tầm kiểm soát**: Thái độ sống, sự tử tế, nỗ lực học hỏi và cách bạn đối xử với những người xung quanh ngay hôm nay.
- **Buông bỏ sự cố chấp với kết quả**: Làm hết sức mình với tâm trong sáng, nhưng sẵn sàng linh hoạt thích nghi nếu hoàn cảnh đổi thay.
- **Xây dựng nội lực vững vàng**: Sự bình yên đích thực không nằm ở một hoàn cảnh bên ngoài hoàn hảo, mà nằm ở một tâm hồn vững chãi trước giông bão.

:::perspective
[life]
#### Lời Khuyên Đời Sống: Bước Từng Bước Vững Chãi Hôm Nay
Đừng để tương lai đè nặng lên đôi vai của bạn. Hãy làm tốt việc của ngày hôm nay, yêu thương người bên cạnh ngày hôm nay. Ngày mai sẽ tự có câu trả lời của ngày mai.
[/dev]
[theravada]
#### Lời Dạy Của Đức Phật: Tự Mình Là Ngọn Đèn Cho Chính Mình
*“Attadīpā viharatha”* — Hãy tự mình là ngọn đèn cho chính mình, hãy lấy Chánh Pháp làm nơi nương tựa vững chãi. Khi tâm an trú trong chánh niệm, bạn sẽ nhìn thấy sự đổi thay của thế gian không phải là tai họa, mà là cánh cửa mở ra sự tự do và giác ngộ.
[/theravada]
:::
MARKDOWN;

        $theravada4Content = <<< 'MARKDOWN'
Làn sóng đổi thay của thời đại đang làm chấn động đời sống con người. Chưa bao giờ nhân loại lại chứng kiến sự biến động diễn ra với tốc độ chóng mặt và khó dự đoán đến thế: Công nghệ mới xuất hiện, kinh tế biến chuyển khôn lường, tương lai đầy ắp những bất định. Bên cạnh sự háo hức là một nỗi sợ hãi bao trùm: Sợ mất việc làm, sợ bị tụt hậu và sợ một tương lai vượt khỏi tầm kiểm soát của con người.

Khi đối diện với những biến động của cuộc đời, người đệ tử Phật tìm thấy nơi nương tựa vững chắc nhất không phải ở các kịch bản dự báo tương lai, mà ở hai ngọn đèn tuệ giác ngàn đời của Đức Phật: **Giáo Lý Duyên Khởi (Paṭiccasamuppāda)** và **Tuệ Tri Tính Vô Thường (Anicca)**.

> *“Sabbe saṅkhārā aniccā’ti, yadā paññāya passati;  
> Atha nibbindati dukkhe, esa maggo visuddhiyā.”*  
> Tất cả các hành là vô thường. Khi thấy được điều ấy bằng trí tuệ, người ấy sẽ nhàm chán khổ đau; đó chính là con đường dẫn đến thanh tịnh.  
> — Kinh Pháp Cú (Dhammapada), Kệ số 277

### 1. Ảo Tưởng Về Quyền Năng Kiểm Soát

Con người đau khổ và lo sợ trước sự bất định vì trong sâu thẳm tâm thức, chúng ta luôn mang một ảo tưởng rằng: *"Tôi phải nắm quyền kiểm soát cuộc đời tôi, tương lai tôi và mọi hoàn cảnh xung quanh tôi"*.

Thế nhưng, Đức Phật đã dạy trong *Kinh Vô Ngã Tướng (Anattalakkhaṇa Sutta)*: Nếu năm uẩn (Sắc, Thọ, Tưởng, Hành, Thức) thực sự là "Ta", là "Của Ta", thì ta có thể ra lệnh cho chúng: *"Hãy như thế này, đừng như thế kia"* được không? Ngay cả một tế bào trong cơ thể hay một cảm xúc khởi lên trong lòng ta còn không thể kiểm soát tuyệt đối, thì làm sao ta có thể đòi hỏi toàn bộ xã hội và dòng chảy vũ trụ phải đứng yên theo ý mình?

Vạn vật trong cõi đời này đều là **Pháp Hữu Vi (Saṅkhāra)** được tạo thành từ vô số nhân duyên. Khi các duyên hội tụ thì sinh ra, khi duyên biến đổi thì chuyển hóa, khi duyên hết thì tan rã. Đó là quy luật tự nhiên muôn đời, không có gì đáng để kinh sợ.

:::perspective
[theravada]
#### Tuệ Giác: Đón Nhận Sự Đổi Thay Bằng Tâm Xả (Upekkhā)
Thay vì chống cự lại quy luật Vô thường một cách vô vọng, hành giả thực hành quan sát sự biến chuyển của cuộc đời như những đám mây đổi hình trên bầu trời. Tâm không bám víu vào quá khứ, không phóng dật lo sợ tương lai, chỉ trọn vẹn tỉnh thức trong từng bước chân hiện tại.
[/theravada]
[life]
#### Góc Nhìn Đời Thường: Thích Nghi Thay Vì Cố Chấp
Cuộc sống giống như một dòng sông luôn chảy xiết. Người thông tuệ không cố gắng xây đập ngăn dòng nước, mà học cách bơi lội mềm mại và uyển chuyển thuận theo dòng chảy.
[/dev]
:::

### 2. Trí Tuệ Và Sự Tỉnh Thức Giữa Biến Động

Dù thế giới có đổi thay đến đâu, có những giá trị tối thượng mà không bất kỳ biến cố nào có thể tước đoạt được của con người:
1. **Khả năng Tỉnh Giác và Tự Biết Mình (Sati-Sampajañña)**: Nhận biết rõ ràng tâm mình đang có tham, sân hay lo sợ để tự chuyển hóa và giữ gìn sự bình an nội tại.
2. **Tâm Từ Bi Vô Lượng (Mettā & Karuṇā)**: Lòng trắc ẩn, tình yêu thương chân thành và sự sẻ chia đối với những người xung quanh trong cơn hoạn nạn.

Hãy sống trọn vẹn và tử tế trong từng phút giây hiện tại, bởi vì hiện tại chính là hạt mầm chân thật duy nhất kiến tạo nên tương lai.

:::perspective
[theravada]
#### Lời Phật Dạy: Hãy Tự Mình Là Ngọn Đèn Cho Chính Mình
*“Attadīpā viharatha attasaraṇā anaññasaraṇā”* — Hãy tự mình là ngọn đèn cho chính mình, hãy tự mình nương tựa chính mình, không nương tựa một ai khác; hãy lấy Chánh Pháp làm ngọn đèn, lấy Chánh Pháp làm chỗ nương tựa. Dù thế giới có đổi thay ngả nghiêng, Chánh Pháp vẫn muôn đời soi sáng con đường an lạc bất diệt.
[/theravada]
[life]
#### Bình Yên Từ Bên Trong
Sự an toàn thực sự không đến từ việc tích lũy thật nhiều của cải bên ngoài, mà đến từ một tâm hồn bình an, vững chãi, biết yêu thương và không còn sợ hãi trước những đổi thay của cuộc đời.
[/dev]
:::
MARKDOWN;

        // Clean existing dual-perspective articles if any to prevent duplicate slugs
        $slugs = [
            'chiec-man-hinh-khong-bao-gio-tat-fomo-doi-song',
            'vong-lap-dopamine-fomo-tam-tri-dev',
            'ai-thu-con-khat-trien-mien-fomo-vipassana',
            'cuoc-dua-khong-co-vach-dich-ap-luc-thanh-cong',
            'chay-he-thong-burnout-overclocking-cuoc-doi-dev',
            'day-dan-gay-vua-van-trung-dao-chua-lanh-burnout',
            'tam-guong-vo-cua-su-so-sanh-vo-boc-xa-hoi',
            'vanity-metrics-va-ao-anh-profile-dev',
            'can-benh-so-sanh-va-bay-nga-man-theravada',
            'dung-giua-nhung-dieu-khong-the-doan-truoc-bat-an',
            'ao-tuong-kiem-soat-bat-dinh-ky-nguyen-ai-dev',
            'duyen-khoi-tu-tai-truoc-bat-toan-ai-theravada',
        ];
        Article::whereIn('slug', $slugs)->delete();

        // -------------------------------------------------------------
        // CREATE ARTICLES
        // -------------------------------------------------------------

        // PAIR 1: FOMO & CƠN NGHIỆN MÀN HÌNH
        $life1 = Article::create([
            'site_domain' => 'main',
            'title' => 'Chiếc Màn Hình Không Bao Giờ Tắt: Nỗi sợ bị bỏ rơi và cái giá của sự chú ý',
            'slug' => 'chiec-man-hinh-khong-bao-gio-tat-fomo-doi-song',
            'category' => 'doi-song',
            'excerpt' => 'Một góc nhìn chân thực về thói quen vô thức chạm vào điện thoại mỗi khi cô đơn, nỗi bất an khi nhìn thấy cuộc sống lộng lẫy của người khác và hành trình tìm lại sự bình yên giữa thế giới ngập tràn thông báo.',
            'content' => $life1Content,
            'tags' => ['fomo', 'doi-song', 'tam-ly', 'mang-xa-hoi', 'binh-yen'],
            'reading_time_min' => 7,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(4),
        ]);

        $theravada1 = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Ái Thủ và Cơn Khát Triền Miên: Quán sát cảm thọ nghiện ngập trong thế giới số',
            'pali_title' => 'Taṇhā, Upādāna & Vedanānupassanā',
            'slug' => 'ai-thu-con-khat-trien-mien-fomo-vipassana',
            'category' => 'phap-hanh',
            'author' => 'Ma Tọa Thiền (Chiêm nghiệm Vipassanā)',
            'excerpt' => 'Quán chiếu bản chất Duyên Khởi của Cảm thọ (Vedanā) và Ái dục (Taṇhā) trong thời đại bùng nổ kích thích giác quan, cùng phương pháp thiết lập chánh niệm đối trị tâm phóng dật.',
            'content' => $theravada1Content,
            'tags' => ['ái thủ', 'cảm thọ', 'fomo', 'vipassana', 'tứ niệm xứ'],
            'pali_terms' => [
                ['term' => 'Taṇhā', 'meaning' => 'Cơn khát ái dục, thèm muốn tìm kiếm kích thích cảm thọ liên tục.'],
                ['term' => 'Upādāna', 'meaning' => 'Sự bám víu, chấp thủ chặt chẽ vào cảm thọ lạc hay tin tức mới.'],
                ['term' => 'Vedanā', 'meaning' => 'Cảm thọ (lạc, khổ, bất lạc bất khổ) khởi sinh khi 6 căn tiếp xúc 6 trần.'],
                ['term' => 'Satipaṭṭhāna', 'meaning' => 'Tứ Niệm Xứ — phương pháp quán sát thân thọ tâm pháp tỉnh giác không đồng hóa.']
            ],
            'reading_time_min' => 8,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(4),
        ]);

        // Link Pair 1
        $life1->update(['paired_article_id' => $theravada1->id]);
        $theravada1->update(['paired_article_id' => $life1->id]);

        // PAIR 2: ÁP LỰC THÀNH CÔNG & GUỒNG QUAY KIỆT SỨC
        $life2 = Article::create([
            'site_domain' => 'main',
            'title' => 'Cuộc Đua Không Có Vạch Đích: Khi sự bận rộn trở thành một tấm huy chương độc hại',
            'slug' => 'cuoc-dua-khong-co-vach-dich-ap-luc-thanh-cong',
            'category' => 'doi-song',
            'excerpt' => 'Nỗi ám ảnh phải thành công trước tuổi 30, cảm giác tội lỗi mỗi khi nằm nghỉ và cái giá đắt đỏ của việc vắt kiệt thể xác lẫn tâm hồn cho những kỳ vọng của xã hội.',
            'content' => $life2Content,
            'tags' => ['burnout', 'ap-luc', 'thanh-cong', 'chua-lanh', 'doi-song'],
            'reading_time_min' => 8,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(3),
        ]);

        $theravada2 = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Dây Đàn Gảy Vừa Vặn: Trí tuệ Trung Đạo giải phóng khỏi kiệt quệ tâm thức',
            'pali_title' => 'Majjhimā Paṭipadā & Indriyasamatta Sutta',
            'slug' => 'day-dan-gay-vua-van-trung-dao-chua-lanh-burnout',
            'category' => 'phap-hanh',
            'author' => 'Ma Tọa Thiền (Chiêm nghiệm Vipassanā)',
            'excerpt' => 'Bài học kinh điển về dây đàn tỳ-bà của Tỳ-kheo Soṇa: Cách quân bình giữa Tinh Tấn Lực (Vīriya) và Định Lực (Samādhi) để hóa giải cơn khủng hoảng kiệt quệ thân tâm.',
            'content' => $theravada2Content,
            'tags' => ['trung đạo', 'burnout', 'tinh tấn', 'sona', 'cân bằng căn lực'],
            'pali_terms' => [
                ['term' => 'Majjhimā Paṭipadā', 'meaning' => 'Con đường Trung Đạo — tránh xa hai cực đoan khổ hạnh ép xác và buông lung dục lạc.'],
                ['term' => 'Vīriya', 'meaning' => 'Tinh tấn lực — năng lượng cần cù, dũng mãnh chân chánh.'],
                ['term' => 'Samādhi', 'meaning' => 'Định lực — sự an trú vững chãi, thanh tịnh của tâm thức.'],
                ['term' => 'Indriyasamatta', 'meaning' => 'Sự quân bình hài hòa giữa năm căn lực: Tín, Tấn, Niệm, Định, Tuệ.']
            ],
            'reading_time_min' => 8,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(3),
        ]);

        // Link Pair 2
        $life2->update(['paired_article_id' => $theravada2->id]);
        $theravada2->update(['paired_article_id' => $life2->id]);

        // PAIR 3: CĂN BỆNH SO SÁNH & VỎ BỌC HÀO NHOÁNG
        $life3 = Article::create([
            'site_domain' => 'main',
            'title' => 'Tấm Gương Vỡ Của Sự So Sánh: Chúng ta đang sống cho mình hay cho ánh nhìn của người khác?',
            'slug' => 'tam-guong-vo-cua-su-so-sanh-vo-boc-xa-hoi',
            'category' => 'doi-song',
            'excerpt' => 'Nhìn thẳng vào thói quen so kè ngầm trong các cuộc gặp gỡ, cái bẫy sống ảo để tìm kiếm sự ngưỡng mộ và hành trình buông bỏ vỏ bọc để tìm về sự chân thật.',
            'content' => $life3Content,
            'tags' => ['so-sanh', 'song-ao', 'tam-ly', 'chan-that', 'doi-song'],
            'reading_time_min' => 7,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(2),
        ]);

        $theravada3 = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Căn Bệnh So Sánh & Bẫy Ngã Mạn: Quán chiếu sự rỗng không của Cái Tôi số',
            'pali_title' => 'Māna, Sakkāya-diṭṭhi & Anattā',
            'slug' => 'can-benh-so-sanh-va-bay-nga-man-theravada',
            'category' => 'phap-hoc',
            'author' => 'Ma Tọa Thiền (Chiêm nghiệm Vipassanā)',
            'excerpt' => 'Phân tích 3 hình thái vi tế của Ngã Mạn (Māna): So hơn, So bằng, và Mặc cảm tự ti; thấu suốt bản chất Vô Ngã để tâm bất động trước tám ngọn gió đời thị phi.',
            'content' => $theravada3Content,
            'tags' => ['ngã mạn', 'so sánh', 'vô ngã', 'sakkaya-ditthi', 'mana'],
            'pali_terms' => [
                ['term' => 'Māna', 'meaning' => 'Kiêu mạn, tâm so sánh mình hơn người, bằng người hoặc tự ti kém người.'],
                ['term' => 'Sakkāya-diṭṭhi', 'meaning' => 'Thân kiến — ảo tưởng xem ngũ uẩn là một cái tôi độc lập thường hằng.'],
                ['term' => 'Anattā', 'meaning' => 'Vô ngã — bản chất không có một chủ thể độc lập, trường tồn bất biến.'],
                ['term' => 'Upekkhā', 'meaning' => 'Xả tâm — tâm thái bình thản, tự tại trước tám ngọn gió đời (Bát Phong).']
            ],
            'reading_time_min' => 8,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(2),
        ]);

        // Link Pair 3
        $life3->update(['paired_article_id' => $theravada3->id]);
        $theravada3->update(['paired_article_id' => $life3->id]);

        // PAIR 4: NỖI BẤT AN TƯƠNG LAI & KHỦNG HOẢNG BẤT ĐỊNH
        $life4 = Article::create([
            'site_domain' => 'main',
            'title' => 'Đứng Giữa Những Điều Không Thể Đoán Trước: Học cách sống cùng sự bất định',
            'slug' => 'dung-giua-nhung-dieu-khong-the-doan-truoc-bat-an',
            'category' => 'doi-song',
            'excerpt' => 'Khi mọi kế hoạch 5 năm hay 10 năm đều có thể sụp đổ sau một biến cố: Làm sao để tìm thấy điểm tựa tinh thần vững chãi giữa một cuộc đời luôn thay đổi?',
            'content' => $life4Content,
            'tags' => ['bat-dinh', 'lo-au', 'tuong-lai', 'thich-nghi', 'doi-song'],
            'reading_time_min' => 8,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(1),
        ]);

        $theravada4 = Article::create([
            'site_domain' => 'theravada',
            'title' => 'Duyên Khởi & An Nhiên Trước Vô Thường: Đón nhận sự đổi thay bằng tâm thái rộng mở',
            'pali_title' => 'Paṭiccasamuppāda & Sabbe Saṅkhārā Aniccā',
            'slug' => 'duyen-khoi-tu-tai-truoc-bat-toan-ai-theravada',
            'category' => 'phap-hoc',
            'author' => 'Ma Tọa Thiền (Chiêm nghiệm Vipassanā)',
            'excerpt' => 'Khảo sát bản chất Vô Thường và Duyên Khởi của vạn vật; giữ vững sự an trú nơi Chánh Pháp để làm chủ tâm thức giữa một thế giới đầy biến động khôn lường.',
            'content' => $theravada4Content,
            'tags' => ['duyên khởi', 'vô thường', 'bất định', 'chánh niệm', 'chấp thủ'],
            'pali_terms' => [
                ['term' => 'Paṭiccasamuppāda', 'meaning' => 'Thập Nhị Duyên Khởi — vạn pháp do vô số nhân duyên hội tụ mà sinh, duyên tan mà diệt.'],
                ['term' => 'Anicca', 'meaning' => 'Vô thường — bản chất biến dịch, không ngừng trôi chảy của mọi hiện tượng hữu vi.'],
                ['term' => 'Saṅkhāra', 'meaning' => 'Hành uẩn / Các pháp hữu vi — mọi sự vật hiện tượng được tạo tác do điều kiện.'],
                ['term' => 'Sampajañña', 'meaning' => 'Tỉnh giác — sự sáng suốt, thấy biết rõ ràng mục đích và sự chân thực của hành động.']
            ],
            'reading_time_min' => 8,
            'is_published' => true,
            'published_at' => $now->copy()->subDays(1),
        ]);

        // Link Pair 4
        $life4->update(['paired_article_id' => $theravada4->id]);
        $theravada4->update(['paired_article_id' => $life4->id]);
    }
}
