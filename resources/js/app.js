import "./bootstrap";

import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";

// Register any Alpine plugins here if needed
// Alpine.plugin(yourPlugin);

// Exposed for inline blade scripts
window.Livewire = Livewire;
window.Alpine = Alpine;

Livewire.start();
