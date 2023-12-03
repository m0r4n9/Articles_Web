<form method="post" class="answer__form">
    <div class="form__wrapper">
        <label for="answer">Ваш ответ:</label>
        <textarea id="answer" name="answer" required></textarea>
    </div>
    <input type="submit" value="Ответить" class="answer__submit">
    <input type="hidden" value=". <?php $id ?> . " name="question_id">
</form>