import HttpClient from '../index'

class EducationApi extends HttpClient {
    list(page = 1, query) {
        return this.requestType('get').request(`/educations?page=${page}&${query}`)
    }

    get(slug) {
        return this.requestType('get').request(`/educations/${slug}/edit`)
    }

    save(payload) {
        return this.requestType('post').formBody(payload).request(`educations`)
    }

    update(payload) {
        return this.requestType('post').formBody(payload).request(`educations/${payload.id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`educations/${id}`)
    }

}

const educationApi = new EducationApi()
export default educationApi
