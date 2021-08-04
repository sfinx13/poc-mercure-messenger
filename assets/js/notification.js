const APP_URL = process.env.APP_URL;
const TOPICS = JSON.parse(document.querySelector('section').dataset.topics);

(async () => {
    const SUBSCRIBE_URL = new URL(process.env.MERCURE_HUB_URL);
    TOPICS.forEach(topic => {
        SUBSCRIBE_URL.searchParams.append('topic', topic);
    })

    const eventSource = new EventSource(SUBSCRIBE_URL.toString(), {
        withCredentials: true,
    });

    const countNotificationElement = document.getElementById('count-notifications');

    eventSource.onmessage = (messageEvent) => {
        const data = JSON.parse(messageEvent.data);
        const id = data.id;
        const content = data.message;
        const link = data.link;
        const createdAt = data.createdAt;
        addNotification(id, createdAt, content, link);
        countNotificationElement.textContent = data.count_notifications;
    };

    eventSource.addEventListener('update-notification', (messageEvent) => {
        const data = JSON.parse(messageEvent.data);
        const notificationElement = document.querySelector(`[data-id='${data.notification_id}']`);
        countNotificationElement.textContent = data.count_notifications;

        if (data.is_read) {
            notificationElement.classList.add('read');
        }
        if (data.is_processed) {
            const btnAction = notificationElement.lastElementChild;
            console.log(btnAction.classList.contains('action'));
            if (btnAction.classList.contains('action')) {
                notificationElement.classList.add('read');
                btnAction.classList.add('process');
                console.log(btnAction);
            }
        }
    });

})();

const notificationCenter = document.getElementById('notification-center');

function addNotification(id, createdAt, content, link = null) {
    let div = document.createElement('div');
    div.dataset.id = id;
    div.dataset.action = "is_read";
    div.classList.add("alert", "alert-dark", "d-flex", "flex-wrap");
    let notification = `<div class="pr-5">${createdAt}</div>`;
    notification += `<div class="flex-grow-1">${content}</div>`;

    if (content.includes('.csv')) {
        notification += `<div class="pl-5 action">
                            <a data-id="${id}" data-action="is_processed" href="${link}" class="btn btn-primary active" role="button">Télécharger</a>
                            <a data-id="${id}" data-action="is_processed" class="btn btn-secondary active" role="button">Annuler</a>
                        </div>`;
    }
    div.innerHTML = notification;
    notificationCenter.prepend(div);
}

notificationCenter.addEventListener('click', function (e) {
    const notificationId = e.target.dataset.id || e.target.parentNode.dataset.id;
    const action = e.target.dataset.action || e.target.parentNode.dataset.action;
    const url = `${APP_URL}/api/notification/${notificationId}/action`;
    const data = {
        action: action
    }

    return fetch(url, {
        method: 'PATCH',
        body: JSON.stringify(data),
    })
        .then(response => {
            if (200 === response.status) {
                return response.json();
            }
        })
        .then(data => console.log(data));
});