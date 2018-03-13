<br>
<b>csrf_token 檢查測試</b><br><br>
有 csrf_token <br>
<form method="post">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    Name:
    <input type="text" name="name">
    <input type="submit">
</form>
<br>
無 csrf_token <br>
<form method="post">
    Name:
    <input type="text" name="name">
    <input type="submit">
</form>