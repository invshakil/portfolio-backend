import HttpClient from '../index'

class WorkplaceApi extends HttpClient {
    list(page = 1, query) {
        return this.requestType('get').request(`/workplaces?page=${page}&${query}`)
    }
    get(slug) {
        return this.requestType('get').request(`/workplaces/${slug}/edit`)
    }

    save(payload) {
        return this.requestType('post').formBody(payload).request(`workplaces`)
    }

    update(payload) {
        return this.requestType('post').formBody(payload).request(`workplaces/${payload.id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`workplaces/${id}`)
    }
}

const workplaceApi = new WorkplaceApi()
export default workplaceApi
