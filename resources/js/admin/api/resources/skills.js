import HttpClient from '../index'

class SkillsApi extends HttpClient {
    get(page = 1) {
        return this.requestType('get').request(`/skills?page=${page}`)
    }

    getDetails(id) {
        return this.requestType('get').request(`/skills/${id}`)
    }

    save(payload) {
        return this.requestType('post').formBody(payload).request(`skills`)
    }

    update(payload, id) {
        return this.requestType('patch').formBody(payload).request(`skills/${id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`skills/${id}`)
    }
}

const skillsApi = new SkillsApi()
export default skillsApi
