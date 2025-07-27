import axios from "axios"

const api = axios.create({
    baseURL: 'https://shameal.ru/shortlink-api',
})

export default api