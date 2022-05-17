import HttpClient from '../index'

class SettingsApi extends HttpClient {
    get(page = 1) {
        return this.requestType('get').request(`/settings?page=${page}`)
    }

    getDetails(id) {
        return this.requestType('get').request(`/settings/${id}`)
    }

    save(payload) {
        return this.requestType('post').formBody(payload).request(`settings`)
    }

    update(payload, id) {
        return this.requestType('patch').formBody(payload).request(`settings/${id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`settings/${id}`)
    }
}

const settingsApi = new SettingsApi()
export default settingsApi
