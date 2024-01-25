# Gestion-BVI
This was a project made in an internship. The point was to create a website to take care of bills.
For this project i used:
- **PHP** as my backend language
- **Javascript** mixed with **JQuery** as my frontend language
- the library **tfpdf** to create and export custom pdfs
- **fontawesome** for some minor additions

the structure of this project follows the principle of **MVC** (Model View Controller):
- Classes, functions and Data Access Objects are written in the model folder.
- the view folder is mostly the html output/the frontend of the website.
- and the controller folder is the backend of the project.

for data storage i used the **DBMS phpmyadmin** coupled with **MariaDB**.
to retrieve the data from my database to my project, i made **DAO** (Data Access Objects) classes. They connect to the server using the php library **PDO**.



PS: this project was for my french studies so the comments, classes, files and folders are in french.
