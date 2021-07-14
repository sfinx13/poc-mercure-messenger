const MERCURE_URL = new URL(process.env.MERCURE_PUBLISH_URL);
const APP_URL = process.env.APP_URL;

MERCURE_URL.searchParams.append('topic', `${APP_URL}/notification`);
MERCURE_URL.searchParams.append('topic', `${APP_URL}/percentage`);
MERCURE_URL.searchParams.append('topic', `${APP_URL}/counter`);
MERCURE_URL.searchParams.append('topic', `${APP_URL}/delete`);

const eventSource = new EventSource(MERCURE_URL.toString());
eventSource.onmessage = result => handle(result);
eventSource.onerror = () => showMessage(
    'warning',
    'Please start the mercure server to be notified in real-time',
    5000);

let countClick = 0;

const exportNumber = document.getElementById('export-number');
const table = document.getElementById('export-list');
const tbody = document.querySelector('tbody');
const btnExport = document.getElementById('btn-export');
const btnDeleteFiles = document.getElementById('btn-delete-files');

btnExport.addEventListener('click', exportFile);
btnDeleteFiles.addEventListener('click', deleteFiles);

exportNumber.innerHTML = countClick.toString();

function displayCount(counter) {
    exportNumber.innerHTML = counter;
}

function removeTableRows() {
    document.querySelectorAll('table#export-list tbody tr').forEach(el => {el.remove()});
}

function addTableRow(filename, size, exportedAt) {
    if (table.classList.contains('d-none')) {
        table.classList.remove('d-none');
    }

    const row = tbody.insertRow(0);
    row.style.backgroundColor = '#55efc4';

    const filenameCell = row.insertCell(0);
    const sizeCell = row.insertCell(1);
    const exportedAtCell = row.insertCell(2);

    filenameCell.innerHTML = `<a href="/download/${filename}">${filename}</a>`;
    sizeCell.innerHTML = size;
    exportedAtCell.innerHTML = exportedAt;

    setTimeout(() => {
        row.style.backgroundColor = '#ffffff';
    }, 2000);
}

function exportFile() {
    const projectId = Math.floor(Math.random() * 100);
    const interval = Math.floor(Math.random() * 10);
    const startDate = new Date().toISOString();
    const url = `${APP_URL}/export?project-id=${projectId}&start-date=${startDate}&interval=${interval}`;

    fetch(url, {
        mode: 'no-cors'
    })
        .then(response => {
            console.log(response);
            if (200 === response.status) {
                showMessage('success', 'Export will start in few minutes...', 2000);
                return response.json();
            } else {
                showMessage('danger', 'Something went wrong on the API', 2000);
            }
        }).then( data => displayCount(data));
}

function deleteFiles() {
    if (!btnDeleteFiles.classList.contains('disabled')) {
        const url = `${APP_URL}/files?extension=csv`;

        fetch(url, {method: 'DELETE'})
            .then(response => {
                if (204 === response.status) {
                    showMessage('warning', 'Removing will start in few minutes...', 500);
                }
            })
    } else {
        showMessage('danger', 'Please wait before deleting', 2000);
    }
}

function showMessage(type, message, timeout) {
    document.querySelector('button.btn.btn-danger').insertAdjacentHTML('afterend',
        `<div class="mt-3 alert alert-${type}">${message}</div>`);
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        alert.parentNode.removeChild(alert);
        document.getElementById("progress-bar-percentage").classList.add('d-none');
    }, timeout);
}

function handle(result) {
    const data = JSON.parse(result.data);
    const currentDate = new Date().toLocaleString();

    if (data.hasOwnProperty('percentage')) {
        document.getElementById('btn-delete-files').classList.add('disabled');
        document.getElementById("progress-bar-percentage").classList.remove('d-none');
        const progressBar = document.getElementById("dynamic");
        const percentage = Math.round(data.percentage);
        progressBar.setAttribute('style', `width:${percentage}%`);
        progressBar.setAttribute('aria-valuenow', percentage);
        progressBar.textContent = `${percentage}% Complete`;
    }

    if (data.hasOwnProperty('filename')) {
        const message = `Filename ${data.filename} has been created at ${currentDate}`;
        showMessage('success', message, 3000);
        addTableRow(data.filename, data.size, currentDate);
        document.getElementById('btn-delete-files').classList.remove('disabled')
    }

    if (data.hasOwnProperty('counter')) {
        displayCount(data.counter);
    }

    if (data.hasOwnProperty('delete')) {
        const message = data.delete;
        removeTableRows();
        showMessage('success', message, 3000);
    }
}

