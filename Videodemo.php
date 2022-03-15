<link rel="stylesheet" href="//assets-cdn.ziggeo.com/v1-stable/ziggeo.css" />


<script src="//assets-cdn.ziggeo.com/v1-stable/ziggeo.js"></script>
<script>ZiggeoApi.token = "80022bf8c53e76bfb6c1bebccefc6113";</script>
<ziggeo ziggeo-limit=15
    ziggeo-width=320
    ziggeo-height=240>
</ziggeo>	

<script>
ZiggeoApi.Events.on("submitted", function (data) {
    alert("The video with token " + data.video.token + " has been submitted!");
});
</script>