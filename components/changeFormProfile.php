<div id="editForm" class="form__edit" style="display: none;">
    <!-- Форма для редактирования данных -->
    <form style="display: flex; flex-direction: column;" onsubmit="saveChanges(); return false;" method="post">

        <div class="input__wrapper">
            <label for="newUsername">Новое имя:</label>
            <input type="text" name="username" id="newUsername" required>
        </div>

        <div class="input__wrapper">
            <label for="newEmail">Новая почта:</label>
            <input type="email" name="email" id="newEmail" required>
        </div>

        <button style="cursor: pointer" type="submit">Сохранить изменения</button>
    </form>
</div>