import $ from 'jquery'
class MyNotes{
    constructor(){
        this.events();
    }
    events(){
        $(".delete-note").on("click",this.deleteNode);
    }
    //Methods
    deleteNode(event){
        let thisNote = $(event.target).parents("li");
       $.ajax({
           beforeSend : (xhr)=>{
               xhr.setRequestHeader('X-WP-Nonce',universityData.nonce)
           },
           url : universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
           type:'DELETE',
           success : (response)=>{
            thisNote.slideUp()
            console.log('Success')
            console.log(response);
           },
           error : (response)=>{
               console.log('Failure')
               console.log(response)
           }
       })
    }
}
export default MyNotes;