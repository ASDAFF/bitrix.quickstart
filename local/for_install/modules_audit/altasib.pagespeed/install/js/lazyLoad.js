class LazyLoad{
    constructor(){
        this.init();
    }
    init(){
        document.addEventListener('DOMContentLoaded', function(){
            var images = document.getElementsByTagName('img');
            for (var i = 0; i < images.length; ++i)
            {
                images[i].src = images[i].getAttribute("data-src")
            }
        });
    }
}
let lazyLoad = new LazyLoad();