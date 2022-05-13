import HttpClient from '../index'

class ProjectsApi extends HttpClient {
    list(page = 1, query) {
        return this.requestType('get').request(`/projects?page=${page}&${query}`)
    }

    getAllPages() {
        return this.requestType('get').request(`/fetch-all-published-projects`)
    }

    get(slug) {
        return this.requestType('get').request(`/projects/${slug}/edit`)
    }

    save(payload) {
        let url = this.requestType('post');

        if (payload.image !== null) {
            url = url.isMultimedia();
        }

        return url.formBody(payload).request(`projects`)
    }

    update(payload) {
        let url = this.requestType('post');

        if (payload.image !== null) {
            url = url.isMultimedia();
        }
        return url.formBody(payload).request(`projects/${payload.id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`projects/${id}`)
    }
}

const projectsApi = new ProjectsApi()
export default projectsApi
