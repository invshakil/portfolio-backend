import HttpClient from '../index'

class PageApi extends HttpClient {

    list(page = 1, query) {
        return this.requestType('get').request(`/news?page=${page}&${query}`)
    }
    getAllNews() {
        return this.requestType('get').request(`/fetch-all-published-news`)
    }

    get(slug) {
        return this.requestType('get').request(`/news/${slug}/edit`)
    }

    save(payload) {
        let url = this.requestType('post');

        if (payload.image !== null) {
            url = url.isMultimedia();
        }

        return url.formBody(payload).request(`news`)
    }


    update(payload) {
        return this.requestType('post').formBody(payload).request(`news/${payload.id}`)
    }

    delete(id) {
        return this.requestType('delete').request(`news/${id}`)
    }

    saveStatus(payload) {
        return this.requestType('post').formBody(payload).request(`save-news-status`)
    }
    deleteNews(id) {
        return this.requestType('delete').request(`delete-news/${id}`)
    }
}

const pageApi = new PageApi()
export default pageApi
