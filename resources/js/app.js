import './bootstrap';

// 1. Importe o Livewire E o Alpine
// Os scripts importados vão se auto-inicializar
// no momento certo (pois o @vite é "defer" por padrão).
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm.js';

// 2. Apenas exponha o Alpine ao 'window' para que o x-data funcione
window.Alpine = Alpine;

// NÃO PRECISA MAIS DE DOMCONTENTLOADED OU .START()
