(function(){

    var MediaLibraryTaxonomyFilter = wp.media.view.AttachmentFilters.extend({
        id: 'media-attachment-wps-ic-filter',

        createFilters: function() {
            var filters = {};
            _.each( WpsIcFilters.filters || {}, function( value, index ) {
                filters[ index ] = {
                    text: value,
                    props: {
                        wps_ic_filters_ajax: index,
                    }
                };
            });
            filters.all = {

                text:  WpsIcFilters.filter_all,
                props: {

                    wps_ic_filters_ajax: '',
                },
                priority: 10
            };
            this.filters = filters;
        }
    });

    var AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
    wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
        createToolbar: function() {
            // Make sure to load the original toolbar
            AttachmentsBrowser.prototype.createToolbar.call( this );
            this.toolbar.set( 'MediaLibraryWpsIcFilter', new MediaLibraryTaxonomyFilter({
                controller: this.controller,
                model:      this.collection.props,
                priority: -75
            }).render() );
        }
    });

})()