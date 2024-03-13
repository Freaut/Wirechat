
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/style.css">
</head>
<?php
  $server = $_SESSION['server'] ? $_SESSION['server'] : "Default";
?>
<div class="textarea" id="chatbox">
    <?php
    if(file_exists("log_".$server.".html") && filesize("log_".$_SESSION['server'].".html") > 0){
        $contents = file_get_contents("log_".$server.".html");
        echo $contents;
    }
    ?>
  </div>
    <form name="message" action="" autocomplete="off">
        <div style="display: flex; align-items: center;">
            <input name="usermsg" type="text" id="usermsg" class="form-control" style="width: 90%" placeholder="Message">
            <input name="submitmsg" type="submit" id="submitmsg" class="btn btn-primary" style="width: 10%" value="Send" />
        </div>
    </form>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
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
                url: '<?php echo("log_".$server.".html") ?>',
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
    
    setInterval (loadLog, 2500); // We do a little refreshing (I fix)
    document.getElementById('chatbox').scrollTop = document.getElementById('chatbox').scrollHeight;
});
</script>