import HttpClient from '../index'

class ServicesApi extends HttpClient {
    getServices(page = 1) {
        return this.requestType('get').request(`/services?page=${page}`)
    }

    getDetails(id) {
        return this.requestType('get').request(`/services/${id}`)
    }
    getSkills() {
        return this.requestType('get').request(`/services/skills}`)
    }

    saveService(payload) {
        return this.requestType('post').formBody(payload).request(`services`)
    }

    updateService(payload, id) {
        return this.requestType('patch').formBody(payload).request(`services/${id}`)
    }

    deleteService(id) {
        return this.requestType('delete').request(`services/${id}`)
    }
}

const servicesApi = new ServicesApi()
export default servicesApi
