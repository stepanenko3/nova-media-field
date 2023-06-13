import IndexField from './components/Fields/IndexField'
import DetailField from './components/Fields/DetailField'
import FormField from './components/Fields/FormField'
import Gallery from './components/Gallery'
import GalleryModal from './components/GalleryModal'
import CustomPropertiesModal from './components/CustomPropertiesModal'
import GalleryItem from './components/GalleryItem'
import GalleryPicture from './components/GalleryPicture'
// import Dropzone from './components/Dropzone'
import { createPinia } from 'nova-file-manager'
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
