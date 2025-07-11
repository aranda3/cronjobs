
<style>
  
  body {
    overflow-x: hidden;
  }

  #sidebar {
    width: 250px;
    position: fixed;
    top: 0;
    left: -250px;
    height: 100vh;
    background-color:rgb(52, 91, 129);
    color: white;
    transition: left 0.3s;
    z-index: 1040;
    padding-top: 60px;
  }

  #sidebar.show {
    left: 0;
  }

  #sidebar .nav-link {
    color: white;
    padding: 10px 20px;
  }

  #sidebar .nav-link:hover {
    background-color:rgb(90, 132, 173);
  }

  #content {
    transition: margin-left 0.3s;
    margin-left: 0;
  }

  #content.shifted {
    margin-left: 250px;
  }

  #sidebarToggleBtn {
    position: absolute;
    bottom: 0%;
    right: -20px;
    transform: translateY(-50%);
    background-color:rgb(52, 91, 129);
    border-radius: 0 5px 5px 0;
    padding: 10px 10px;
    cursor: pointer;
    z-index: 1050;
    /*border: 1px solid #444;*/
  }

  #sidebarArrow {
    color: white;
    font-size: 1.2rem;
    user-select: none;
  }

</style>
