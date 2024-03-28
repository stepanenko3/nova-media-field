import IndexField from './components/Fields/IndexField.vue'
import DetailField from './components/Fields/DetailField.vue'
import FormField from './components/Fields/FormField.vue'
import Gallery from './components/Gallery.vue'
import GalleryModal from './components/GalleryModal.vue'
import CustomPropertiesModal from './components/CustomPropertiesModal.vue'
import GalleryItem from './components/GalleryItem.vue'
import GalleryPicture from './components/GalleryPicture.vue'
// import Dropzone from './components/Dropzone'
import { createPinia } from '@vendor/stepanenko3/nova-filemanager/dist/js/package.js'
import '../css/field.css'

Nova.booting((app, store) => {
    app.use(createPinia());

    app.component('index-nova-media-field', IndexField)
    app.component('detail-nova-media-field', DetailField)
    app.component('form-nova-media-field', FormField)
    app.component('Gallery', Gallery)
    app.component('GalleryItem', GalleryItem)
    app.component('GalleryModal', GalleryModal)
    app.component('GalleryPicture', GalleryPicture)
    app.component('CustomPropertiesModal', CustomPropertiesModal)
    // app.component('Dropzone', Dropzone)
})
