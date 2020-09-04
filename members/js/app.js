const memberChoices = document.querySelectorAll('[data-members="choices"]');
if(memberChoices) {
    memberChoices.forEach(e => {
        let opts = {
            duplicateItemsAllowed: false
        };
        
        if(e.hasAttribute('data-max')) {
            opts.maxItemCount = e.dataset.max;
        }


        let choices = new Choices(e, opts);
        let newChoices = [];
        


        e.addEventListener('search', function(ev) {
            
            
            let query = ev.detail.value;
            
            if(query.length > 2) {
                var formData = new FormData();
                formData.append('q', query);
                formData.append('token', Perch.token);

                // search
                fetch(`${Perch.path}/addons/fieldtypes/members/search.php` , {
                    method: 'post',
                    body: formData
                })
                .then(function( response ) {
                    return response.json();
                }).then(function( data ) {
                    

                    let options = data.filter(function(option) {
                        return (
                            !newChoices.some(e => e.value === option.value) &&
                            !choices.config.choices.some(e => e.value === option.value)
                        )
                    })
                    
                    if(options) {
                        choices.setChoices(options);
                        newChoices = newChoices.concat(options)
                    }
                    
        

                })
                .catch(function( error ) {
                    console.log( error );
                });
            }
            

        })
    })
}
