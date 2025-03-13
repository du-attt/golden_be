## Giới thiệu
- Dự án này bao gồm một ứng dụng frontend (React) và một ứng dụng backend (Laravel), cả hai đều được container hóa bằng Docker. Frontend sử dụng Vite để tối ưu hóa trải nghiệm phát triển, còn backend sử dụng Laravel. Cả hai ứng dụng sẽ được triển khai cùng nhau thông qua Docker Compose.

## Yêu cầu
- Docker và Docker Compose (có thể cài đặt Docker Destop)
## Cài đặt dự án
1. Clone dự án về máy
2. Sau khi clone truy cập vào thư mục dự án và chạy các lệnh dưới đây
// Lệnh dùng để tạo database và tạo dữ liệu từ file
- php artisan migrate --seed --class=ExamResultsSeeder 


// Lệnh dùng để tải các tập lệnh cần thiết và build dự án
- docker-compose up --build
3. Sau khi chạy bước 2 xong truy cập vào đường dẫn dưới
- http://localhost:9000/
