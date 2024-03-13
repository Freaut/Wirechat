// MOVED TO CHAT.PHP TO INTEGRATE PHP FOR SERVERS

$(document).ready(function () {
    $("#submitmsg").click(function () {
        var clientmsg = $("#usermsg").val();

        if (clientmsg.length > 0 && clientmsg.length < 1000) {
            $.post("../chat/post.php", { text: clientmsg });
            $("#usermsg").val("");
            setTimeout(function() { loadLog(); }, 100);
            return false;
        }
    });
    
    function loadLog() {
        document.getElementById("chatbox").scrollHeight = 0;
        if (chatbox != null) {
        var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request
        $.ajax({
            url: "log.html",
            cache: false,
            success: function (html) {
            $("#chatbox").html(html); //Insert chat log into the #chatbox div
        
            //Auto-scroll           
            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request
            if(newscrollHeight > oldscrollHeight){
                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
            }   
            }
        });
        }
    }
    
    setInterval (loadLog, 2500); // refresh time
    document.getElementById('chatbox').scrollTop = document.getElementById('chatbox').scrollHeight;
});