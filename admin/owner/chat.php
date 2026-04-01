<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';

renderHeader('Support Chat');
renderSidebarMenu('chat', 'owner');
renderMainContentStart('Support Chat', $_SESSION['username'] ?? 'Owner');
?>

<style>
.chat-page{
    display:flex;
    flex-direction:column;
    gap:20px;
}

.chat-box{
    height:400px;
    overflow-y:auto;
    display:flex;
    flex-direction:column;
    gap:10px;
    padding:10px;
    background:#f8fafc;
    border-radius:12px;
}

.msg{
    max-width:70%;
    padding:10px 14px;
    border-radius:14px;
    background:#e2e8f0;
    font-size:14px;
}

.msg.self{
    align-self:flex-end;
    background:#ff6b35;
    color:#fff;
}

.chat-form{
    display:flex;
    gap:10px;
    margin-top:10px;
}

.chat-form textarea{
    flex:1;
    resize:none;
    border:1px solid #ddd;
    border-radius:10px;
    padding:10px;
}

.chat-form button{
    background:#ff6b35;
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    cursor:pointer;
}

@media(max-width:768px){
    .chat-box{height:300px;}
    .msg{max-width:90%;}
}
</style>

<div class="chat-page">

    <!--<div class="card">-->
    <!--    <button onclick="startConversation()">Start / Refresh Conversation</button>-->
    <!--</div>-->

    <div class="card">
        
        <div>Coming Soon</div>
        <!--<div class="chat-box" id="ownerChatMessages"></div>-->

        <!--<form id="ownerChatForm" class="chat-form">-->
        <!--    <input type="hidden" name="conversation_id" id="ownerConversationId">-->
        <!--    <textarea name="message" placeholder="Type message..." required></textarea>-->
        <!--    <button type="submit">Send</button>-->
        <!--</form>-->
    </div>

</div>

// <script>
// function escapeHtml(v){
//     if(!v) return '';
//     const d=document.createElement('div');
//     d.textContent=v;
//     return d.innerHTML;
// }

// function scrollBottom(){
//     const box=document.getElementById('ownerChatMessages');
//     box.scrollTop=box.scrollHeight;
// }

// async function startConversation(){
//     try{
//         const res=await apiGet('../api/owner/support-chat');

//         if(res.status==='success'){
//             document.getElementById('ownerConversationId').value=res.data.conversation_id;
//             loadOwnerMessages(res.data.conversation_id);
//         }else{
//             showToast(res.message,'error');
//         }

//     }catch(e){
//         showToast('Error starting chat','error');
//     }
// }

// async function loadOwnerMessages(id){
//     const box=document.getElementById('ownerChatMessages');

//     try{
//         const res=await apiGet('../api/owner/support-chat?conversation_id='+id);

//         if(res.status==='success'){
//             let html='';

//             res.data.messages.forEach(function(msg){
//                 html+='<div class="msg '+(msg.sender_type==='owner'?'self':'')+'">'
//                     +escapeHtml(msg.message)
//                     +'</div>';
//             });

//             box.innerHTML = html || '<div>No messages</div>';
//             scrollBottom();
//         }

//     }catch(e){
//         box.innerHTML='Error loading messages';
//     }
// }

// document.getElementById('ownerChatForm').addEventListener('submit', async function(e){
//     e.preventDefault();

//     const id=document.getElementById('ownerConversationId').value;

//     try{
//         const res=await apiPost('../api/owner/support-chat', new FormData(this));

//         showToast(res.message, res.status==='success'?'success':'error');

//         if(res.status==='success'){
//             this.reset();
//             loadOwnerMessages(id);
//         }

//     }catch(e){
//         showToast('Send failed','error');
//     }
// });

// document.addEventListener('DOMContentLoaded', startConversation);
</script>

<?php renderFooter('owner'); ?>