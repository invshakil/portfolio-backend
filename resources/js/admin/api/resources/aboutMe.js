import HttpClient from '../index'

class AboutMeApi extends HttpClient {
    get(page = 1) {
        return this.requestType('get').request(`/about-me?page=${page}`)
    }

    getDetails(id) {
        return this.requestType('get').request(`/about-me/${id}`)
    }

    save(payload) {
        return this.requestType('post').formBody(payload).request(`about-me`)
    }

    update(payload, id) {
        return this.requestType('patch').formBody(payload).request(`about-me/${id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`about-me/${id}`)
    }
}

const aboutMeApi = new AboutMeApi()
export default aboutMeApi
