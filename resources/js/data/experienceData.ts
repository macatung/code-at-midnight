import type { ExperienceItem, DeveloperStat } from '../types/portfolio';

export const experienceData: ExperienceItem[] = [
  {
    id: 'studio-ai-orchestration',
    period: '2025 — Hiện Tại',
    role: 'Lead AI Agent & Autonomous Systems Architect',
    company: 'MacaTung Studio Engineering',
    location: 'Distributed Cloud',
    type: 'Venture',
    summary: 'Thiết kế và triển khai kiến trúc Multi-Agent AI tự trị, tích hợp RAG thời gian thực, Function Calling ERP/Database, và tự động hóa quy trình nghiệp vụ quy mô lớn.',
    achievements: [
      'Kiến trúc Multi-Agent CS giải quyết tự động > 92% khiếu nại khách hàng, giảm 85% chi phí vận hành.',
      'Tích hợp Tool/Function Calling với API ERP/Database tra cứu đơn hàng, tự động đối soát và hoàn tiền với độ trễ < 1.2s.',
      'Triển khai hệ thống RAG thời gian thực với Vector Database, tự động cập nhật tri thức sản phẩm & chính sách bảo hành mới nhất.'
    ],
    technologies: ['Multi-Agent Orchestration', 'Google Gemini AI', 'OpenAI', 'Python / FastAPI', 'Laravel 12', 'Redis Queue', 'Vector DB'],
    midnightQuest: 'Triển khai kiến trúc Multi-Agent tự động chặn 10,000 ca khiếu nại gian lận trong đợt lưu lượng truy cập cao điểm.'
  },
  {
    id: 'studio-telecom-gis',
    period: '02/2022 — 06/2025',
    role: 'Fullstack Developer & Senior Systems Architect',
    company: 'Telecom & Spatial GIS Infrastructure',
    location: 'Enterprise Systems',
    type: 'Full-time',
    summary: 'Chủ trì thiết kế các hệ thống hạ tầng viễn thông, số hóa mạng lưới cáp quang toàn quốc (GIS/QGIS), hệ thống giám sát thiết bị truyền dẫn (NMS) và định tuyến không gian tối ưu.',
    achievements: [
      'Xây dựng hệ thống GIS số hóa mạng cáp quang toàn quốc, tích hợp QGIS phân tích dữ liệu không gian và tự động định tuyến cáp tối ưu.',
      'Phát triển Network Management System (NMS) giám sát thời gian thực thiết bị truyền dẫn SDH/DWDM qua SNMP/Telnet/SSH với cảnh báo dị thường ML.',
      'Xây dựng hệ thống IP Management tính toán subnet tự động, quản lý phân cấp IP quy mô lớn trên Laravel + Filament Admin.'
    ],
    technologies: ['PHP / Laravel', 'Filament Admin', 'Node.js', 'Python (Data/ML)', 'Elasticsearch', 'MySQL', 'Redis', 'RabbitMQ', 'ReactJS', 'QGIS / Spatial GIS', 'SNMP'],
    midnightQuest: 'Tối ưu hóa thuật toán định tuyến cáp quang GIS trên bản đồ 500,000 điểm nút giúp giảm thời gian tính toán từ 12s xuống < 180ms.'
  },
  {
    id: 'studio-streaming-gateway',
    period: '06/2017 — 01/2022',
    role: 'Backend Web Developer & Streaming Engineer',
    company: 'High-Throughput Streaming & Media Architecture',
    location: 'High-Scale Cloud',
    type: 'Full-time',
    summary: 'Kiến trúc nền tảng xử lý và phân phối media/video streaming tải cao, chuyển mã tự động đa độ phân giải và cổng API Gateway bảo mật.',
    achievements: [
      'Xây dựng pipeline chuyển mã video tự động đa độ phân giải (FFmpeg) và phát trực tuyến thích ứng Adaptive Bitrate Streaming (HLS/DASH).',
      'Tích hợp CDN đa tầng tối ưu hóa chi phí băng thông 42% và thời gian tải đệm video xuống sub-second.',
      'Phát triển Laravel API Gateway chịu tải xác thực, phân quyền và rate-limiting hàng triệu requests mỗi ngày với Redis & RabbitMQ.'
    ],
    technologies: ['PHP / Laravel', 'FFmpeg', 'Redis', 'RabbitMQ', 'MySQL', 'Nginx', 'HLS/DASH Streaming', 'Docker'],
    midnightQuest: 'Bảo đảm đường truyền trực tiếp 80,000 CCU không gián đoạn trong các phiên cao điểm bằng cơ chế dynamic CDN failover.'
  },
  {
    id: 'studio-core-foundations',
    period: '2013 — 2018',
    role: 'National Informatics Prodigy & Software Engineer',
    company: 'Algorithmic Research & High-Performance Foundations',
    location: 'Systems Lab',
    type: 'Open Source',
    summary: 'Nghiên cứu thuật toán chuyên sâu, cấu trúc dữ liệu hiệu năng cao, tư duy thiết kế hệ thống chịu lỗi và văn hóa kỹ nghệ phần mềm không thỏa hiệp.',
    achievements: [
      'Nghiên cứu cấu trúc dữ liệu và giải thuật đồ thị quy mô lớn, tối ưu hóa độ phức tạp thời gian và không gian bộ nhớ.',
      'Xây dựng các công cụ tính toán và nền tảng thuật toán cốt lõi với C/C++, Linux và kiến trúc hướng module.',
      'Thiết lập tiêu chuẩn kiểm thử tự động, phòng ngừa hồi quy và văn hóa phát triển phần mềm chất lượng cao.'
    ],
    technologies: ['C/C++', 'Algorithms & Data Structures', 'Linux', 'Git', 'PHP', 'MySQL'],
    midnightQuest: 'Giải quyết các bài toán tối ưu hóa thuật toán phức tạp lúc Midnight, đặt nền móng vững chắc cho các hệ thống chịu tải cao sau này.'
  }
];

export const developerStats: DeveloperStat[] = [
  {
    label: 'Kinh Nghiệm Thực Chiến',
    value: '8+ Năm',
    unit: 'Senior',
    iconName: 'Zap',
    description: 'Kiến trúc hệ thống tải cao, hạ tầng viễn thông & AI Agents'
  },
  {
    label: 'Tỉ Lệ CS Tự Động Hóa',
    value: '92%+',
    unit: 'Auto CS',
    iconName: 'Cpu',
    description: 'Multi-Agent AI xử lý khiếu nại & hoàn tiền 24/7'
  },
  {
    label: 'Hạ Tầng GIS & Thiết Bị',
    value: '500K+',
    unit: 'Nodes',
    iconName: 'Server',
    description: 'Quản lý mạng cáp quang & thiết bị truyền dẫn viễn thông'
  },
  {
    label: 'Uptime Cam Kết Đêm',
    value: '99.99%',
    unit: 'SLA',
    iconName: 'Shield',
    description: 'Kiến trúc chịu tải cao & Zero Downtime 24/7'
  }
];
