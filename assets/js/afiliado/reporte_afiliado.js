// Sidebar toggle
document.getElementById("toggleSidebar").addEventListener("click", () => {
    document.getElementById("sidebar").classList.toggle("active");
    document.querySelector(".main").classList.toggle("shifted");
});