<?php
require_once '../common/layout.php';
renderHeader('Support Wallet');
require_once '../common/auth.php';
requirePageRole([ROLE_SUPPORT]);
?>
<div class="card">
    <form id="supportWalletForm">
        <input type="number" name="user_id" placeholder="User ID" required>
        <input type="number" step="0.01" name="amount" placeholder="Amount" required>
        <select name="type" required>
            <option value="credit">Credit</option>
            <option value="debit">Debit</option>
        </select>
        <input type="text" name="remark" placeholder="Remark">
        <button type="submit">Update Wallet</button>
    </form>
</div>

<script>
document.getElementById('supportWalletForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const res = await apiPost('../api/support/wallet-update.php', new FormData(this));
    showAlert(res.message);
    if(res.status === 'success'){
        this.reset();
    }
});
</script>
<?php renderFooter('support'); ?>