<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Административный");
?>

    <span id="push">Ваша корзина пуста</span>
    <button id="button">Купить</button>
    <br>
    <span id="push">Ваша корзина пуста</span>
    <button id="button">Купить</button>
    <button onclick="alert(buttons)">Выведи переменную на экран</button>
    <script src="script.js"></script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>