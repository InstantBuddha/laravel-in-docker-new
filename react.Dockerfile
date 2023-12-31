FROM node:14-alpine

WORKDIR /react-frontend

RUN npm install

CMD ["npm", "start"]