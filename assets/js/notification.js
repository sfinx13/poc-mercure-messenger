const APP_URL = process.env.APP_URL;
const TOPICS = JSON.parse(document.querySelector('section').dataset.topics);

(async () => {
    const SUBSCRIBE_URL = new URL(process.env.MERCURE_HUB_URL);
    TOPICS.forEach(topic => {
        SUBSCRIBE_URL.searchParams.append('topic', topic);
    })

    const response = await fetch(process.env.MERCURE_HUB_URL + "/subscriptions", {
        credentials: "same-origin"
    });
    let data = null;

    /*    try {
        data = await response.json();
        SUBSCRIBE_URL.searchParams.append('Last-Event-ID', data.lastEventID);
    } catch (error) {
        console.log(error);
    }
*/
    const eventSource = new EventSource(SUBSCRIBE_URL.toString(), {
        withCredentials: true,
    });

    eventSource.onmessage = (messageEvent) => {
        const data = JSON.parse(messageEvent.data);
        const id = data.id;
        const content = data.message;
        const link = data.link;
        const createdAt = data.createdAt;
        addNotification(id, createdAt, content, link);
    };

})();

const notificationCenter = document.getElementById('notification-center');

function addNotification(id, createdAt, content, link = null) {
    let div = document.createElement('div');
    div.id = id;
    div.classList.add("alert", "alert-dark", "d-flex", "flex-wrap");
    let notification = `<div class="pr-5"><a href="">${createdAt}</a></div>`;
    notification += `<div class="flex-grow-1"><a href="">${content}</a></div>`;
    console.log(notification);
    if (content.includes('.csv')) {
        notification += `<div class="pl-5">
                            <a href="${link}" class="btn btn-primary active" role="button">Download</a>
                            <a class="btn btn-secondary active" role="button">Cancel</a>
                        </div>`;
    }
    div.innerHTML = notification;
    notificationCenter.prepend(div);
}