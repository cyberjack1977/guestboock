<div class="modal">
    <form>
        <fieldset>
            <legend>Оставить сообщение</legend>
            <input id="name" type="text" maxlength="32" placeholder="Введите свое имя (цифры и буквы латинского алфавита)" autocomplete="off" required>
            <input id="email" type="email" maxlength="48" placeholder="Введите свой email" autocomplete="off" required>
            <textarea id="message" rows="4" maxlength="256" placeholder="Введите сообщение (до 256 символов)" required></textarea>
        </fieldset>
        <fieldset>
            <legend>Проверка, что вы не робот</legend>
            <span>Пожалуйста введите код на картинке в поле, чтобы мы убедились что вы реальный пользователь нашего ресурса.</span>
            <img id="captcha" src="/captcha.png" alt="Captcha" title="Код">
            <input id="code" type="text" maxlength="4" placeholder="Введите код" autocomplete="off">
            <a href="#" id="refresh" title="Если не можете разобрать код, то обновите его">Обновить код</a>
        </fieldset>
        <span>Нажимая «Отправить», даю согласие на обработку введенной информации.</span>
        <button id="send" class="red" type="button">Отправить</button>
        <button id="cancel" type="button">Отмена</button>
    </form>
</div>