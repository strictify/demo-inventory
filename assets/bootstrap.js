import {startStimulusApp} from '@symfony/stimulus-bundle';
import {Autocomplete} from 'stimulus-autocomplete';

const app = startStimulusApp();
app.register('autocomplete', Autocomplete);

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
