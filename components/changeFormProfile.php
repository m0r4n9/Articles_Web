<div id="editForm" class="form__edit" style="display: none;">
    <!-- Форма для редактирования данных -->
    <form style="display: flex; flex-direction: column;" onsubmit="saveChanges(); return false;" method="post">

        <div class="input__wrapper">
            <label for="username">Новое имя:</label>
            <input type="text" name="username" id="username" value="<?= $user_data["username"] ?>" required>
        </div>

        <div class="input__wrapper">
            <label for="email">Новая почта:</label>
            <input type="email" name="email" id="email" value="<?= $user_data["email"] ?>" required>
        </div>

        <button style="cursor: pointer" class="save-btn" type="submit">Сохранить изменения</button>
        <a style="margin-top: 12px; text-align: center;" href="../profile.php?id=<?= $user_id ?>">Отмена</a>
    </form>
</div>