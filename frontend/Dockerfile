FROM node:20

WORKDIR /app

# Instala dependencias
COPY package*.json ./
# Instalamos dependencias primero para el build, pero no importa si el volumen las "tapa"
RUN npm install --include=dev

COPY . .

EXPOSE 5173

# Usa nuestro entrypoint personalizado
ENTRYPOINT [ "sh", "/app/entrypoint.sh" ]