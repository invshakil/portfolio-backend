import HttpClient from '../index'

class WebsiteApi extends HttpClient {
    hit() {
        return this.requestType('get').request(`/hit`)
    }
    getVisits() {
        return this.requestType('get').request(`/visitCount`)
    }
    getVisitors() {
        return this.requestType('get').request(`/visitorInfo`)
    }
}

const websiteApi = new WebsiteApi()
export default websiteApi
