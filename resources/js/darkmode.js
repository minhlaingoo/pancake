// document.addEventListener('alpine:init', () => {
//     Alpine.data('theme', () => ({
//         theme: 'light',
//         init() {
//             this.theme = localStorage.getItem('theme') || 'light';
//             if (this.theme === 'dark') {
//                 document.documentElement.classList.add('dark');
//                 this.changeThemeIcon();
//             }
//         },
//         switchTheme() {
//             document.documentElement.classList.toggle('dark');
//             this.changeThemeIcon();
//             this.theme = this.theme === 'light' ? 'dark' : 'light';
//             localStorage.setItem('theme', this.theme);
//         },
//         changeThemeIcon() {
//             document.querySelector('.dark-icon').classList.toggle('hidden');
//             document.querySelector('.light-icon').classList.toggle('hidden');
//         }
//     }));
// });