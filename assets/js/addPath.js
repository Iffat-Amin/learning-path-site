
function createTopicCheckBox(topic) {
    let container = document.createElement('div');
    container.className = "form-check form-checkinline";
    let chk = document.createElement('input');
    chk.type = "checkbox";
    chk.name = "topics[]";
    chk.value = topic[0];
    chk.className = "form-check-input";
    let lbl = document.createElement('label');
    lbl.className = "form-check-label";
    lbl.innerHTML = topic[1];
    container.appendChild(chk);
    container.appendChild(lbl);
    document.getElementById('topics-covered').appendChild(container);
}

document.getElementById('category').addEventListener('change', e => {
    let prevTopics = document.getElementById('topics-covered');
    prevTopics.innerHTML = '';
    let categoryName = e.target.options[e.target.selectedIndex].text;
    let topics = categories[categoryName];
    topics.forEach(topic => {
        createTopicCheckBox(topic);
    });
})