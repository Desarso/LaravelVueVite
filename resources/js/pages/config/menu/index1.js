(function () {
    axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
    initDashboard();
})();

function initDashboard() {

    new Vue({
        el: '#dashBoard',
        data() {
            return {
                reports: [],
            }
        },
        
        methods: {
            selectRoom(room) {
            },
        },
        beforeCreate() {
            getReportList().then((reports) => {
                this.reports = reports;
            });
        },
        mounted() {
        }
    });
}

async function getReportList() {

    let url = `${server_url}api/rooms?user_uid=${chat_uid}`
    var response = await axios.get(url, {
        headers: { "Access-Control-Allow-Origin": "*" },
    });

    return response['data']['rooms'];;
}
