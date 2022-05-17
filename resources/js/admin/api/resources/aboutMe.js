import HttpClient from '../index'

class AboutMeApi extends HttpClient {
    get(page = 1) {
        return this.requestType('get').request(`/about-me?page=${page}`)
    }

    getDetails(id) {
        return this.requestType('get').request(`/about-me/${id}`)
    }

    save(payload) {
        let url = this.requestType('post');

        if (payload.image !== null) {
            url = url.isMultimedia();
        }

        return url.formBody(payload).request(`about-me`)
    }

    update(payload, id) {
        let url = this.requestType('patch');

        if (payload.image !== null) {
            url = url.isMultimedia();
        }
        return url.formBody(payload).request(`about-me/${id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`about-me/${id}`)
    }
}

const aboutMeApi = new AboutMeApi()
export default aboutMeApi
