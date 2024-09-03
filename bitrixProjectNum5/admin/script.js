let buttons = document.querySelectorAll('button[id^="button"]');
let push = document.getElementById('push');
let ban = document.getElementById('ban');
for(let button of buttons) {
    if (buttons[1]) {
        button.addEventListener('click', function () {
            push.innerHTML = "Добавлено в корзину";
        })
    }
}
push.onclick = function (){
    push.innerHTML ="Ваша корзина пуста";
}
