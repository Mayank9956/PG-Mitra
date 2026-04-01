<?php
require_once '../common/layout.php';
renderHeader('Wallet Management');
require_once '../common/auth.php';
requirePageRole([ROLE_ADMIN]);
?>
<div class="card">
    <h3>Update Wallet</h3>
    <form id="walletForm">
        <input type="number" name="user_id" placeholder="User ID" required>
        <input type="number" step="0.01" name="amount" placeholder="Amount" required>
        <select name="type" required>
            <option value="credit">Credit</option>
            <option value="debit">Debit</option>
        </select>
        <input type="text" name="remark" placeholder="Remark">
        <button type="submit">Submit</button>
    </form>
</div>

<div class="card">
    <h3>Wallet History</h3>
    <input type="number" id="walletHistoryUserId" placeholder="Enter User ID">
    <button onclick="loadWalletHistory()">Load History</button>
</div>

<div id="walletHistoryList"></div>

<script>
document.getElementById('walletForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const res = await apiPost('../api/admin/wallet-update.php', new FormData(this));
    showAlert(res.message);
    if(res.status === 'success'){
        this.reset();
    }
});

async function loadWalletHistory() {
    const userId = document.getElementById('walletHistoryUserId').value;
    const res = await apiGet('../api/admin/wallet-history.php?user_id=' + encodeURIComponent(userId));
    let html = '';
    if (res.status === 'success') {
        html += `<div class="card"><h3>Balance: ₹${res.data.balance}</h3></div>`;
        res.data.transactions.forEach(item => {
            html += `
                <div class="card">
                    <p><strong>${item.type.toUpperCase()}</strong> - ₹${item.amount}</p>
                    <p>${item.remark || ''}</p>
                    <p>${item.created_at}</p>
                </div>
            `;
        });
    }
    document.getElementById('walletHistoryList').innerHTML = html;
}
</script>
<?php renderFooter('admin'); ?>