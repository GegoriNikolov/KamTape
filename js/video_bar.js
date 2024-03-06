
    var imageBrowsers = new Object();
    var images_loaded = false;

    function shiftLeft(bar_id) {
        if (images_loaded == false) {
            return;
        }
        imageBrowsers[bar_id].shiftPageLeft();
        imageBrowsers[bar_id].showImages();
    }
    function shiftRight(bar_id) {
        if (images_loaded == false) {
            return;
        }
        imageBrowsers[bar_id].shiftPageRight();
        imageBrowsers[bar_id].showImages();
    }
    
    // CLASS: ImageBrowser: Stores the array of ktImage and does stuff with them
    function ImageBrowser(display_count, step_size, root_div_id) {
        this.display_count = display_count;
        this.step_size = step_size;
        this.root_div_id = root_div_id;
        this.display_array = new Array();
        
        this.addImage = imageBrowserAddImage;
        this.initDisplay = imageBrowserInitDisplay;

        this.shiftPageLeft = imageBrowserShiftPageLeft;
        this.shiftPageRight = imageBrowserShiftPageRight;

        this.shiftLeft = imageBrowserShiftLeft;
        this.shiftRight = imageBrowserShiftRight;
        this.showImages = imageBrowserShowImages;
        this.prefetchNextPrevImages = imageBrowserPrefetchNextPrevImages;
        this.printPageNumbers = imageBrowserPrintPageNumbers;
		this.incrementIndex = imageBrowserIncrementIndex;
		this.decrementIndex = imageBrowserDecrementIndex;
    }
    function imageBrowserAddImage(ktImg) {
        if (!this.images) {
            var images=new Array();
            this.images = images;
        }
        //ktImg.title1 = "Index: " + this.images.length;
        this.images.push(ktImg);
    }

    function imageBrowserInitDisplay() {
        this.real_images_count = this.images.length;
        
        // Fill with empty images to match page size.
        var empty_slots = this.display_count - (this.images.length % this.display_count);
        if (empty_slots != this.display_count) {
            for (var i=0; i<empty_slots; i++) {
                this.addImage(new ktImage("img/pixel.gif", "", "&nbsp;", "", "&nbsp;", "", true));
            }
        }
        
        for (var i=0; i<this.display_count; i++) {
            this.display_array[i] = this.images[i];
        }

        this.head_index = 0;
        this.tail_index = this.display_array.length-1;
    }
	function imageBrowserIncrementIndex(index) {
        index = index +1;
        if (index > this.images.length-1) {
            index = 0;
        }
		return index;
        
	}
	function imageBrowserDecrementIndex(index) {
        index = index -1;
        if (index < 0) {                     
            index = this.images.length - 1;
        } 
		return index;
	}

    function imageBrowserShiftPageRight() {
        for(var i=0; i<this.display_array.length; i++) {
            this.shiftRight();
        }
    }
    function imageBrowserShiftPageLeft() {
        for(var i=0; i<this.display_array.length; i++) {
            this.shiftLeft();
        }
    }
	function imageBrowserShiftRight() {
        // First shift all elements in display_array, and fill the last one.
        for (var i=0; i< this.display_array.length; i++) {
            this.display_array[i] = this.display_array[i+1];
        }
		this.head_index = this.incrementIndex(this.head_index);
		this.tail_index = this.incrementIndex(this.tail_index);

        this.display_array[this.display_array.length-1] = this.images[this.tail_index];
    }
    
    function imageBrowserShiftLeft() {        
        for (var i=this.display_array.length-1; i > 0; i--) {
            this.display_array[i] = this.display_array[i-1];
        }
		this.head_index = this.decrementIndex(this.head_index);
		this.tail_index = this.decrementIndex(this.tail_index);

		this.display_array[0] = this.images[this.head_index];
    }

    function imageBrowserShowImages() {
        for (var i=0; i<this.display_array.length; i++) {
            block = document.getElementById("img_" + this.root_div_id + "_" + i);
            title1 = document.getElementById("title1_" + this.root_div_id + "_" + i);
            title2 = document.getElementById("title2_" + this.root_div_id + "_" + i);

            block.src = this.display_array[i].getImage();
            title1.innerHTML = this.display_array[i].title1;
            title2.innerHTML = this.display_array[i].title2;

            imgUrl = document.getElementById("href_" + this.root_div_id + "_" + i);
            imgUrl.href = this.display_array[i].imageUrl;

            maindiv = document.getElementById("div_" + this.root_div_id + "_" + i);
            maindiv_alternate = document.getElementById("div_" + this.root_div_id + "_" + i + "_alternate");
            if(this.display_array[i].is_dummy) {
                maindiv.style.display="none";
                maindiv_alternate.style.display="";
            } else {
                maindiv.style.display="";
                maindiv_alternate.style.display="none";
            }
       }
       this.printPageNumbers();
       //this.prefetchNextPrevImages();
    }
	
	
    function imageBrowserPrefetchNextPrevImages() {
		// Prefetch the next and previous image
		prev_index = this.decrementIndex(this.head_index);
		this.images[prev_index].loadImage();
		next_index = this.incrementIndex(this.tail_index);
		this.images[next_index].loadImage();
	}
    function imageBrowserPrintPageNumbers() {
        var counter_index = this.head_index;
        for(var i=0; i<this.display_array.length; i++) {
            counter_index = this.incrementIndex(counter_index);
        }
        if (counter_index == 0) {
            counter_index = this.real_images_count;
        }

        counter_div = document.getElementById("counter_" + this.root_div_id);
		if(counter_div) {
			counter_div.innerHTML = "[" + (this.head_index+1) + " - " + counter_index + " of " + this.real_images_count + "]";
		}
    }

    // CLASS ktImage: Store the image and some metadata on it.
    function ktImage(imgSrc, imgUrl, title1, title1Url, title2, title2Url, is_dummy) {
        this.smartImage = new smartImageObject(imgSrc, false);

        this.imageUrl = imgUrl;
        this.is_dummy = is_dummy;
        this.title1_full = title1;
        this.title2_full = title2;

        var max_len = 24;
        if(title1.length > max_len)
            title1 = title1.substring(0, max_len-3) + "..."
            
        if(title2.length > max_len)
            title2 = title2.substring(0, max_len-3) + "..."

        if(title1Url.length > 0)
            this.title1 = "<a href='" + title1Url + "' title='" + this.title1_full + "'>" + title1 + "</a>";
        else 
            this.title1 = title1;

        if(title2Url.length > 0)
            this.title2 = "<a href='" + title2Url + "' title='" + this.title2_full + "'>" + title2 + "</a>";
        else
            this.title2 = title2;

        this.getImage = ktGetImageFromSmartImage;
        this.isImageLoaded = ktIsImageLoaded;
		this.loadImage = ktLoadImage
    }

    // CLASS ktImage: custom image
	function customktImage(imgSrc, imgUrl, title1, title2, is_dummy) {
        this.smartImage = new smartImageObject(imgSrc, false);

        this.imageUrl = imgUrl;
        this.is_dummy = is_dummy;

		this.title1 = title1;
		this.title2 = title2;

        this.getImage = ktGetImageFromSmartImage;
        this.isImageLoaded = ktIsImageLoaded;
		this.loadImage = ktLoadImage
    }

    function ktGetImageFromSmartImage() {
        return this.smartImage.getImage();
    }
	function ktIsImageLoaded() {
		return this.smartImage.isImageLoaded();
	}
	function ktLoadImage() {
		if(!this.isImageLoaded()) {
			this.smartImage.load();
		}
	}

    // CLASS: Smart Image: Pre-loads the image.
	function smartImageObject(imgURI,preload) {
	  this.URI = imgURI;
	  this.imageobj = null;

      // attach object's methods
	  this.load = smartImageObjectLoad; //  force the loading of the image
	  this.getImage = smartImageObjectGetImage;
	  this.isImageLoaded = smartImageIsImageLoaded;
	  if (preload) { 
		this.load();
	  }
	}
	
	function smartImageIsImageLoaded() {
		if(this.imageobj != null)
			return true;
		else
			return false;
	}
	
	function smartImageObjectLoad() {
	  this.imageobj = new Image(); 
	  this.imageobj.src = this.URI;
	}
	
	function smartImageObjectGetImage() {
	  if (this.imageobj) { 
		return this.imageobj.src;
	  } else { 
		this.load();
		return this.URI;
	  }
	}
    // Smart Image End.
