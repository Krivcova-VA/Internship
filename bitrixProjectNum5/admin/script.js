let buttons = document.querySelectorAll('button[id^="button"]');
let push = document.getElementById('push');
let ban = document.getElementById('ban');
for(let button of buttons){
    button.addEventListener('click',function (){
        push.innerHTML = "Добавлено в корзину";
        ban.innerHTML = "не добавлно";
    })
}
push.onclick = function (){
    push.innerHTML ="Ваша корзина пуста";
}
ban.onclick = function (){
    ban.innerHTML ="Ваша корзина не пуста";
}