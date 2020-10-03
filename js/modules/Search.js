import $ from 'jquery'
class Search{
    constructor(){
       this.addSearchHTML();
       this.openButton = $(".js-search-trigger")
       this.closeButton = $(".search-overlay__close")
       this.searchOverlay = $(".search-overlay")
       this.searchField = $("#search-term")
       this.resultsDiv = $("#search-overlay__results")
       this.isSpinnerVisible = false
       this.events()
       this.isOverlayOpen = false;
       this.prevValue;
       this.typingTimer;
    }
    events(){
        this.openButton.on("click",this.openOverlay.bind(this))
        this.closeButton.on("click",this.closeOverlay.bind(this))
        $(document).on("keydown",this.keyPressDispatcher.bind(this))
        this.searchField.on("keyup",this.searchLogic.bind(this))

    }
    searchLogic(){
        if(this.searchField.val() != this.prevValue){

            clearTimeout(this.typingTimer)
            if(this.searchField.val()){

                if(!this.isSpinnerVisible){
                    this.resultsDiv.html('<div class = "spinner-loader"></div>')
                    this.isSpinnerVisible = true;
            
                }
                 this.typingTimer =  setTimeout(this.getResults.bind(this),750)
            }else{
                this.resultsDiv.html(' ')
                this.isSpinnerVisible = false
            }
        }
     this.prevValue = this.searchField.val()
    }
    getResults(){
        $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term='+ this.searchField.val(),(results)=>{
          this.resultsDiv.html(`
          <div class = "row">
          <div class = "one-third">
          <h2 class = "search-overlay__section-title">General Information</h2>
          ${results.generalInfo.length ? `<ul class = 'link-list min-list'>` : '<p>No results found.</p>'}
            ${results.generalInfo.map((item) => `<li><a href = "${item.permalink}" target = "_blank">${item.title}</a>  ${item.postType == 'post' ? `by ${item.authorName}` : ''} </li>`).join('')}
            ${results.generalInfo.length ? `</ul>` : ''}
          </div>
          <div class = "one-third">
          <h2 class = "search-overlay__section-title">Programs</h2>
          ${results.programs.length ? `<ul class = 'link-list min-list'>` : '<p>No results found.</p>'}
            ${results.programs.map((item) => `<li><a href = "${item.permalink}" target = "_blank">${item.title}</a></li>`).join('')}
            ${results.programs.length ? `</ul>` : ''}
          <h2 class = "search-overlay__section-title">Professors</h2>
          ${results.professors.length ? `<ul class = 'professor-cards'>` : '<p>No results found.</p>'}
          ${results.professors.map((item) => `
          <li class="professor-card__list-item">
          <a class = "professor-card" href="${item.permalink}"> 
          <img src="${item.image}" class="professor-card__image" target = '_blank'>
          <span class="professor-card__name">${item.title}</span>
           </a>
          </li>
          `).join('')}
          ${results.professors.length ? `</ul>` : ''}
          </div>
          <div class = "one-third">
          <h2 class = "search-overlay__section-title">Campuses</h2>
          ${results.campuses.length ? `<ul class = 'link-list min-list'>` : '<p>No results found.<a href = "${universityData.root_url}/campuses">View All Campuses</a></p>'}
          ${results.campuses.map((item) => `<li><a href = "${item.permalink}" target = "_blank">${item.title}</a></li>`).join('')}
          ${results.campuses.length ? `</ul>` : ''}

          <h2 class = "search-overlay__section-title">Events</h2>
          ${results.events.length ? '' : `<p>No results found. <a href = "${universityData.root_url}/events">View All Events</a></p>`}
          ${results.events.map((item) => `
          <div class="event-summary">
              <?php $eventDate = new DateTime(get_field('event_date'));?>
              <a class="event-summary__date event-summary__date--beige t-center" href="${item.permalink} target = "_blank">
                <span class="event-summary__month">${item.month}</span>
                <span class="event-summary__day">${item.day}</span>
              </a>
              <div class="event-summary__content">
                <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                <p>${item.description}<a href="${item.permalink}" class="nu gray" target = "_blank">Read more</a></p>
              </div>
            </div>
          `).join('')}
          </div>
          </div>
          `)
          this.isSpinnerVisible = false;
        })
    }
    keyPressDispatcher(e){
        if(e.keyCode == 83 && !this.isOverlayOpen && !$("input,textarea").is(":focus")){
            this.openOverlay()
        }
        if(e.keyCode == 27 && this.isOverlayOpen){
            this.closeOverlay()
        }
    }
    openOverlay(){
        this.searchOverlay.addClass("search-overlay--active")
        this.searchField.val('')
        setTimeout(()=>{this.searchField.focus()},301)
        $("body").addClass("body-no-scroll")
        this.isOverlayOpen = true
    }
     closeOverlay(){
         this.searchOverlay.removeClass("search-overlay--active")
         $("body").removeClass("body-no-scroll")
         this.isOverlayOpen = false
     }
     addSearchHTML(){
         $("body").append(`<div class = 'search-overlay'>
         <div class = 'search-overlay__top'>
         <div class = 'container'>
         <i class = 'fa fa-search search-overlay__icon' aria-hidden = 'true'></i>
         <input type="text" class = 'search-term' placeholder = 'What are you looking for?' id = 'search-term'>
         <i class = 'fa fa-window-close search-overlay__close' aria-hidden = 'true'></i>
         </div>
         </div>
         <div class = "container">
         <div id = 'search-overlay__results'>
           
         </div>
         </div>
         </div>`);
     }   
}
export default Search