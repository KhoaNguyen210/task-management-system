# docker/node/Dockerfile
FROM node:18-alpine

# Thiết lập thư mục làm việc bên trong container
WORKDIR /app

# Sao chép package.json và package-lock.json để cài đặt dependencies
# LƯU Ý: Đảm bảo các file này tồn tại ở thư mục gốc của dự án khi Docker build
COPY package*.json ./

# Cài đặt Node.js dependencies
RUN npm install

# Sao chép toàn bộ mã nguồn ứng dụng
# Đây sẽ là bind mount khi chạy docker-compose, nhưng cần copy ban đầu
COPY . .

# Expose port mặc định của Vite
EXPOSE 5173

# Lệnh mặc định để chạy Vite dev server khi container khởi động
CMD ["npm", "run", "dev"]