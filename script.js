function show() {
    var current = $("div.current");
    
    current.fadeIn("slow");
    
    setTimeout("change()", 5000);
}

function change() {
    var current = $("div.current");
    var next = (current.next().length != 0) ? current.next() : false;
    
    current.fadeOut("slow", function () {
        current.removeClass("current");
        current.hide();
        if (next != false) {
            next.addClass("current");
            show();
        } else {
            loadImages(current.attr("id"));
        }
    });
}
    
function loadImages(after) {
    var url = "./data.php?callback=?";
    
    if (after != null) {
        url = url + "&after=" + after;
    }
    
    if (typeof customUrl != "undefined") {
        url = url + "&url=" + customUrl;
    } else if (typeof subreddit != "undefined") {
        url = url + "&r=" + subreddit;
    }
    
    $.getJSON(url, function (data) {
        $("#images").empty();
        
        $.each(data.images, function(index, image){
            var container = $("<div>").attr("id", image.reddit_id);
            var link = $("<a>").attr("href", image.link).attr("title", image.title);
            var img = $("<img>").attr("src", image.url);
            var descr = $("<p>").text("Posted by " + image.author);
            
            $("#images").append(container.append($("<p>").append(link.append(img))).append(descr));
            
            $("#images div").hide();
            $("#images div:first").addClass("current");
        });
        
        show();
    })
}