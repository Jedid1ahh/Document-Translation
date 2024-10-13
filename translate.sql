-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2024 at 06:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `translate`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `translated_filename` varchar(255) DEFAULT NULL,
  `file_type` enum('PDF','DOCX','CSV') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `filename`, `translated_filename`, `file_type`, `created_at`, `updated_at`) VALUES
(1, 4, 'Lab Practice 1 (1).pdf', '67032d9e71543_translated_Lab Practice 1 (1)..txt', 'PDF', '2024-10-07 00:39:09', '2024-10-07 00:39:09'),
(2, 4, 'OBI JOSEPH CV.docx', '67036276cfd4d_translated_OBI JOSEPH CV..txt', 'DOCX', '2024-10-07 04:24:25', '2024-10-07 04:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `language_pairs`
--

CREATE TABLE `language_pairs` (
  `id` int(11) NOT NULL,
  `source_language` varchar(50) NOT NULL,
  `target_language` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `source_language` varchar(10) NOT NULL,
  `target_language` varchar(10) NOT NULL,
  `original_text` longtext NOT NULL,
  `translated_text` longtext NOT NULL,
  `status` enum('Pending','In Progress','Completed','Reviewed','Rejected') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `document_id`, `source_language`, `target_language`, `original_text`, `translated_text`, `status`, `created_at`, `updated_at`) VALUES
(5, 1, 'English', 'Spanish', 'Database Management System \nLab Practice 1 \n1 | P a g e\n\nEx.No: 1 \nSQL BASIC COMMANDS \n \n//Create table  \nCREATE TABLE emp  \n( \nempno NUMBER, \nempname VARCHAR2(255),  \nDOB DATE, \nsalary NUMBER,  \ndesignation VARCHAR2(20) \n); \n \n \n// Insert values \nINSERT INTO emp VALUES(100,\'John\', \'21-Apr-1996\',  50000,\'Manager\'); \nINSERT INTO emp VALUES(101,\'Greg\', \'20-July-1994\',  2500,\'Clerk\'); \n \n// Display values \nSELECT * FROM emp; \nEMPNO \n100 \n101 \nEMPNAME \nJohn  \nGreg \nDOB \n21-Apr-1996 \n20-July-1994 \nSALARY \n50000 \n2500 \nDESIGNATION \nManager  \nClerk \nSELECT empname,salary FROM emp; \nEMPNAME \nJohn  \nGreg \nSALARY \n50000 \n25000 \n2 | P a g e\n\n// Modify values \nUPDATE emp SET salary = salary + 1000; \n \n \nSELECT * FROM emp; \nEMPNO EMPNAME DOB SALARY DESIGNATION \n100 John 21-Apr-1996 51000 Manager \n101 Greg 20-July-	1994 3500 Clerk \n// Delete values \nDELETE FROM emp WHERE empno = 100; \n \n \nSELECT * FROM emp; \nEMPNO EMPNAME DOB SALARY DESIGNATION \n101 Greg 20-July-	1994 3500 Clerk \nResult: \nThus all the above basic SQL commands has been executed successfully and the output was verified. \n3 | P a g e\n\nEx.No: 2 \nData Definition Language (DDL) \nDDL Statements \n \nCREATE TABLE \nALTER TABLE \nDROP TABLE \n \n1. CREATE Statement \nThe CREATE TABLE statement is used to create a relational table  \nCREATE TABLE table_name \n( \ncolumn_name1 data_type [constraints], \ncolumn_name1 data_type [constraints],  \ncolumn_name1 data_type [constraints], \n…….. \n); \n \n2. Alter Table \n \nThe ALTER TABLE statement is used to add, delete, or modify columns in an existing table \n \na.To Add a column \nALTER TABLE table_name ADD column_name datatype  \nb. To delete a column in a table \nALTER TABLE table_name DROP COLUMN column_name  \nc. To change the data type of a column in a table \nALTER TABLE table_name ALTER COLUMN column_name datatype \n \n3. Drop Table \nUsed to delete the table permanently from the storage  \nDROP TABLE table_name \n4 | P a g e\n\nEx. No: 2.a \nData Definition Language (DDL) \n(Without constraint) \n1. CREATE THE TABLE \n \nCREATE TABLE emp  \n( \nempno NUMBER,  \nempname VARCHAR2(25),  \ndob DATE, \nsalary NUMBER,  \ndesignation VARCHAR2(20) \n); \n \n- Table Created \n \n// Describe the table emp \n \nDESC emp; \nColumn Data Type Length PrecisionScale Primary Key Nullable Default Comment \nEMPNO NUMBER 22 - - - - - - \nEMPNAME VARCHAR2 25 - - - - - - \nDOB DATE 7 - - - - - - \nSALARY NUMBER 22 - - - - - - \nDESIGNATION VARCHAR2 20 - - - - - - \n2. ALTER THE TABLE \n \na.ADD \n// To alter the table emp by adding new attribute department  \nALTER TABLE emp ADD department VARCHAR2(50); \n5 | P a g e\n\nDESC emp; \nColumn Data Type Length Precision Scale Primary Key Nullable Default Comment \nEMPNO NUMBER 22 - - - - - - \nEMPNAME VARCHAR2 25 - - - - - - \nDOB DATE 7 - - - - - - \nSALARY NUMBER 22 - - - - - - \nDESIGNATION VARCHAR2 20 - - - - - - \nDEPARTMENT VARCHAR2 50 - - - - - - \nb. MODIFY \n// To alter the table emp by modifying the size of the attribute department \n \nALTER TABLE emp MODIFY department VARCHAR2(100); \nDESC emp; \nColumn Data Type Length Precision Scale Primary Key Nullable Default Comment \nEMPNO NUMBER 	22 - - - - - - \nEMPNAME VARCHAR2 25 - - - - - - \nDOB DATE 7 - - - - - - \nSALARY NUMBER 22 - - - - - - \nDESIGNATION VARCHAR2 20 - - - - - - \nDEPARTMENT VARCHAR2 100 - - - - - - \nc. DROP \n// To alter the table emp by deleting the attribute department \n \nALTER TABLE emp DROP(department); \nLength PrecisionScale Primary Key Nullable Default Comment Data Type \nNUMBER 22 \nVARCHAR2 25 \nDATE 7 \nNUMBER 22 \nDESC emp;  \nColumn  \nEMPNO  \nEMPNAME  \nDOB  \nSALARY \nDESIGNATION VARCHAR2 20 \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n- \n6 | P a g e\n\nd. RENAME \n// To alter the table name by using rename keyword \n \nALTER TABLE emp RENAME TO emp1 ; \nDESC emp1; \nColumn Data Type Length PrecisionScale Primary Key Nullable Default Comment \nEMPNO NUMBER 22 - - - - - - \nEMPNAME VARCHAR2 25 - - - - - - \nDOB DATE 7 - - - - - - \nSALARY NUMBER 22 - - - - - - \nDESIGNATION VARCHAR2 20 - - - - - - \nDEPARTMENT VARCHAR2 100 - - - - - - \n3. DROP \n//To delete the table from the database \n \nDROP TABLE emp1; \n \nDESC emp1; \nObject to be described could not be found. \n7 | P a g e\n\nEx. No: 2.b \nData Definition Language (DDL) \n(With constraint) \nConstraints Types \n \nNOT NULL \nUNIQUE \nPRIMARY KEY \nFOREIGN KEY \nCHECK \nDEFAULT \n1. CREATE THE TABLE \n \n// To create a table student \n \nCREATE TABLE student  \n( \nstudentID NUMBER PRIMARY KEY,  \nsname VARCHAR2(30) NOT NULL, \ndepartment CHAR(5),  \nsem NUMBER, \ndob DATE, \nemail_id VARCHAR2(20) UNIQUE,  \ncollege VARCHAR2(20) DEFAULT \'MEC\' \n); \n \n// Describe the table student \n \nDESC student; \nColumn Data Type Length Precision ScalePrimary KeyNullableDefault Comment \nSTUDENTID NUMBER 22 - - 1 - - 	- \nSNAME VARCHAR2 30 - - - - - 	- \nDEPARTMENT CHAR 5 - - - - - 	- \nSEM NUMBER 22 - - - - - 	- \nDOB DATE 7 - - - - - 	- \nEMAIL_ID VARCHAR2 20 - - - - - 	- \nCOLLEGE VARCHAR2 20 - - - - 	\'MEC\' - \n8 | P a g e\n\n//To create a table exam \n \nCREATE TABLE exam  \n( \nexamID NUMBER , \nstudentID NUMBER REFERENCES student(studentID),  \ndepartment CHAR(5) NOT NULL, \nmark1 NUMBER CHECK (mark1<=100 and mark1>=0),  \nmark2 NUMBER CHECK (mark2<=100 and mark2>=0), \nmark3 NUMBER CHECK (mark3<=100 and mark3>=0), \nmark4 NUMBER CHECK (mark4<=100 and mark4>=0), \nmark5 NUMBER CHECK (mark5<=100 and mark5>=0), \ntotal NUMBER,  \naverage NUMBER,  \ngrade CHAR(1) \n); \n \n \n//Describe the table exam \nDESC exam; \nColumn Data Type Length Precision Scale Primary Key Nullable Default Comment \nEXAMID NUMBER 22 - - - - - - \nSTUDENTID NUMBER 22 - - - - - - \nDEPARTMENT CHAR 5 - - - - - - \nMARK1 NUMBER 22 - - - - - - \nMARK2 NUMBER 22 - - - - - - \nMARK3 NUMBER 22 - - - - - - \nMARK4 NUMBER 22 - - - - - - \nMARK5 NUMBER 22 - - - - - - \nTOTAL NUMBER 22 - - - - - - \nAVERAGE NUMBER 22 - - - - - - \nGRADE CHAR 1 - - - - - - \n2. ALTER THE TABLE \n \nA. ADD \n//To alter the table student by adding new attribute address \n \nALTER TABLE student ADD address VARCHAR2(100); \n9 | P a g e\n\nDBMS LAB MANUAL, Department of Information Technology, SMVEC. \nDESC student; \nColumn Data Type Length Precision ScalePrimary Key NullableDefault Comment \nSTUDENTID NUMBER 22 	- - 1 - - - \nSNAME VARCHAR2 30 - - - - - - \nDEPARTMENT CHAR 5 	- - - - - 	- \nSEM NUMBER 22 	- - - - - 	- \nDOB DATE 7 	- - - - - 	- \nEMAIL_ID VARCHAR2 20 - - - - - 	- \nCOLLEGE VARCHAR2 20 - - - - 	\'MEC\' - \nADDRESS VARCHAR2 100 - - - - - 	- \n//To alter the table student by adding new constraint to the examID attribute \n \nALTER TABLE exam ADD CONSTRAINT pr PRIMARY KEY (examID); \n \nB. MODIFY \n//To alter the table student by modifying the size of the attribute address  \nALTER TABLE student MODIFY address VARCHAR2(150); \nDESC student; \nColumn Data Type Length Precision Scale Primary Key Nullable Default Comment \nSTUDENTID NUMBER 22 - - 1 - - 	- \nSNAME VARCHAR2 30 - - - - - 	- \nDEPARTMENT CHAR 5 - - - - - 	- \nSEM NUMBER 22 - - - - - 	- \nDOB DATE 7 - - - - - 	- \nEMAIL_ID VARCHAR2 20 - - - - - 	- \nCOLLEGE VARCHAR2 20 - - - - 	\'MEC\' - \nADDRESS \n \nC. DROP \nVARCHAR2 150 - - - - - 	- \n//To alter the table student by deleting the attribute address \n \nALTER TABLE student DROP(address); \n10 | P a g e\n\nDESC student; \nColumn Data Type Length \n- \n- \n- \n- \n- \n- \nPrecision ScalePrimary KeyNullableDefault Comment \n- \n- \n- \n- \n- \n- \nSTUDENTID NUMBER 22 - - 1 	- \nSNAME VARCHAR2 30 - - - 	- \nDEPARTMENT CHAR 5 - - - 	- \nSEM NUMBER 	22 - - - 	- \nDOB DATE 	7 - - - 	- \nEMAIL_ID VARCHAR2 20 - - - 	- \nCOLLEGE VARCHAR2 20 - - - 	- \'MEC\' - \nD. RENAME \n// To alter the table name by using rename keyword \n \nALTER TABLE student RENAME TO student1 ;  \nTable altered \nDESC student1; \n \nColumn \n \nData Type \n \nLength \n \nPrecision ScalePrimary KeyNullableDefault Comment \nSTUDENTID NUMBER 22 - - 1 - - - \nSNAME VARCHAR2 30 - - - - - - \nDEPARTMENT CHAR 5 - - - - - - \nSEM NUMBER 	22 - - - - - - \nDOB DATE 	7 - - - - - - \nEMAIL_ID VARCHAR2 20 - - - - - - \nCOLLEGE VARCHAR2 20 - - - - \'MEC\' - \nALTER TABLE student1 RENAME TO student ;  \nTable altered \n \n \n3. DROP \n \n// To delete the table from the database \n \nDROP TABLE exam; \n \n \nDESC exam; \nObject to be described could not be found. \n \n \nResult: \nThus all the above DDL SQL commands has been executed successfully and the output was verified. \n11 | P a g e\n\nSQL FOREIGN KEY Constraint \n \nA FOREIGN KEY is a key used to link two tables together. \nA FOREIGN KEY is a field (or collection of fields) in one table that refers to the \nPRIMARY KEY in another table. \nThe table containing the foreign key is called the child table, and the table \ncontaining the candidate key is called the referenced or parent table. \n \nExample \nSee the following two tables: \n \n \n \n \n \n \n \n \n \n \n \n \nNotice that the \"PersonID\" column in the \"Orders\" table points to the \n\"PersonID\" column in the \"Persons\" table. \nThe \"PersonID\" column in the \"Persons\" table is the PRIMARY KEY in the \n\"Persons\" table. \nThe \"PersonID\" column in the \"Orders\" table is a FOREIGN KEY in the \"Orders\" \ntable. \nThe FOREIGN KEY constraint is used to prevent actions that would destroy links \nbetween tables. \nThe FOREIGN KEY constraint also prevents invalid data from being inserted into \nthe foreign key column, because it has to be one of the values contained in the \ntable it points to.\n\nSQL FOREIGN KEY on CREATE TABLE \nThe following SQL creates a FOREIGN KEY on the \"PersonID\" column when \nthe \"Orders\" table is created: \n \nCREATE  TABLE  Orders ( \n    OrderID  int NOT NULL PRIMARY KEY, \n    OrderNumber int NOT NULL, \n    PersonID int FOREIGN KEY REFERENCES Persons(PersonID) \n);  \n \nSQL FOREIGN KEY on ALTER TABLE \n \nTo create a FOREIGN KEY constraint on the \"PersonID\" column when the \n\"Orders\" table is already created, use the following SQL: \n \nALTER TABLE Orders \nADD CONSTRAINT FK_PersonOrder \nFOREIGN KEY (PersonID) REFERENCES Persons(PersonID);  \n \nDROP a FOREIGN KEY Constraint \nTo drop a FOREIGN KEY constraint, use the following SQL: \n \nALTER TABLE Orders \nDROP CONSTRAINT FK_PersonOrder;\n\nEx.No: 3 \nDATA MANIPULATION LANGUAGE (DML) \n \nDML Statements \n \nInsert into \nUpdate \nDelete \n \n1. Insert into \n \nThe INSERT INTO statement is used to insert a new row in a table. \n \nDifferent forms of inserting a new record into the table \n \na. Direct Substitution \n \nINSERT INTO table_name VALUES(value1, value2,value3, ……….); \n \nb.Specific Column Insertion \n \nINSERT INTO table_name (columnane1, columnname2, columnname3, …..)  \nVALUES (values1, value2,value3, ………) ; \n \nc.Macro Substitution, this query is used to receive values at runtime. \n \nINSERT INTO table_name VALUES(&columnname1,&columnname2, ….); \n \n2. Update \n \nUpdate new data into an existing table \n \nSyntax \n \nUPDATE table_name SET column1=value, column2=value2,... WHERE some_column=some_value; \n \n3. Delete Query \n \nThe DELETE query is used to delete rows in a table. \n \nSyntax \n \nDELETE FROM table_name WHERE column1=somevalue; \n14 | P a g e\n\n1. INSERT \n// To insert the values as rows in the table student  \nDirect substitution \nINSERT INTO student VALUES(101,\'RUPESH\',\'IT\',5,\'04/18/1996\',\'rupesh@gmail.com\',\'MEC\');  \nINSERT INTO student VALUES (102,\'BALA\',\'CSE\',7,\'10/7/1995\',\'bala@gmail.com\',\'IIT\'); \nINSERT INTO student VALUES (104,\'HEMESH\',\'IT\',5,\'7/23/1996\',\'hemesh@gmail.com\',\'IIT\'); \n \nINSERT INTO student VALUES (106,\'SAIVAISHNAVI\',\'CSE\',5,\'06/9/1996\', \'vaishu@gmail.com\',\'IFET\'); \n \nINSERT INTO student(studentid,sname,department,sem,dob,email_id) \nVALUES (108,\'RISHA\',\'IT\',5,\'04/21/1996\',\'risha@gmail.com\'); //(For the purpose of default constraint- \nSpecific Column Insertion) \n// To display all the records in the table student \n \nSELECT * FROM student; \nSTUDENTID \nCOLLEGE \nSNAME DEPARTMENT SEM  DOB EM AIL_ID \n101 RUPESH IT 5 04/18/1996 rupesh@gmail.com MEC \n102 BALA CSE 7 10/07/1995 bala@gmail.com IIT \n104 HEMESH IT 5 07/23/1996 hemesh@gmail.com IIT \n106 SAI VAISHNAVI CSE 5 06/09/1996 vaishu@gmail.com SMVEC \n108 RISHA IT 5 04/21/1996 risha@gmail.com MEC \n// To insert the values as rows in the table exam \n \nSpecific Column Insertion \n \nINSERT INTO exam(examid, studentid, department, mark1, mark2, mark3, mark4, mark5)  \nVALUES (2222,101,\'IT\',98,87,83,99,87); \n \nINSERT INTO exam(examid, studentid, department, mark1, mark2, mark3, mark4,mark5)  \nVALUES(3333,104,\'IT\',99,82,84,89,100); \n \nINSERT INTO exam(examid, studentid, department, mark1, mark2, mark3, mark4,mark5)  \nVALUES(4444,108,\'IT\',92,85,83,91,87); \n15 | P a g e\n\nMacro Substitution \n \n \nINSERT INTO exam(&examid, &studentid, &department, &mark1, &mark2, &mark3,&mark4,&mark5)  \nVALUES(5555,106,\'CSE\',82,85,87,91,85); \n \nEnter the value for examid : 5555  \nEnter the value for studentid: 106  \nEnter the value for department: CSE  \nEnter the value for mark1: 82 \nEnter the value for mark2: 85  \nEnter the value for mark3: 87  \nEnter the value for mark4: 91  \nEnter the value for mark5: 85 \n \nold 1: INSERT INTO exam VALUES(&examid); INSERT INTO exam  \nVALUES(5555,106,‟CSE‟,82,85,87,91,85) \nnew 1: INSERT INTO exam VALUES(5555,106,‟CSE‟,82,85,87,91,85) \n \n \n// To display all the records in the table exam \n \nSELECT * FROM exam; \nMARK5 TOTAL AVERAGE EXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4  \nGRADE \n2222 \n- \n101 IT 98 87 83 99 87 - - - \n3333 104 IT 99 82 84 89 100 - - - \n- \n4444 108 IT 92 85 83 91 87 - - - \n- \n5555 106 CSE 82 85 87 91 85 - - - \n- \n2. UPDATE \n \n// To change the values in the table student \n \nUPDATE student SET college=\'MEC\' WHERE studentid=108;  \n1 row(s) updated \n16 | P a g e\n\n// To display the updated value in the table student \n \nSELECT * FROM student; \nSTUDENTID SNAME DEPARTMENT SEM DOB EMAIL_ID COLLEGE \n101 RUPESH IT 5 04/18/1996 rupesh@gmail.com MEC \n102 BALA CSE 	7 10/07/1995 bala@gmail.com IIT \n104 HEMESH IT 5 07/23/1996 hemesh@gmail.com IIT \n106 SAI VAISHNAVI CSE 5 06/09/1996 vaishu@gmail.com SMVEC \n108 RISHA IT 5 04/21/1996 risha@gmail.com MEC \n//To set the total in the table exam \n \nUPDATE exam SET total=(mark1+mark2+mark3+mark4+mark5);  \n4 row(s) updated \n \n//To display the updated value in the table exam \n \nSELECT * FROM exam; \nMARK5 TOTAL AVERAGE EXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4  \nGRADE \n2222 \n- \n101 IT 98 87 83 99 87 454 - - \n3333 104 IT 99 82 84 89 100 454 - - \n- \n4444 108 IT 92 85 83 91 87 438 - - \n- \n5555 106 CSE 82 85 87 91 85 430 - - \n- \n//To set the average in the table exam \n \nUPDATE exam SET average=total/5; \n17 | P a g e\n\nDBMS LAB MANUAL, Department of Information Technology, SMVEC. \n//To display the updated value in the table exam \n \nSELECT * FROM exam; \n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE  \nGRADE \n2222 \n- \n101 IT \n- \n98 87 83 99 87 454 90.8 	- \n3333 104 IT 99 82 84 89 100 454 90.8 	- \n- - \n4444 108 IT 92 85 83 91 87 438 87.6 	- \n- - \n5555 106 CSE 82 85 87 91 85 430 86 	- \n- - \n//To set the grade in the table exam \n \nUPDATE exam SET grade=\'S\' WHERE average>95; \nUPDATE exam SET grade=\'A\' WHERE average<=95 AND average>90; \nUPDATE exam SET grade=\'B\' WHERE average<=90 AND average>85; \nUPDATE exam SET grade=\'C\' WHERE average<=85 AND average>80; \nUPDATE exam SET grade=\'D\' WHERE average<=80 AND average>75; \nUPDATE exam SET grade=\'F\' WHERE average<75; \n \n \n//To display the updated values in the table exam \n \nSELECT * FROM exam; \n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE  \nGRADE \n2222 101 IT 98 87 83 99 87 454 	90.8 \nA \n3333 104 IT 99 82 84 89 100 454 	90.8 \nA \n4444 108 IT 92 85 83 91 87 438 	87.6 \nB \n5555 106 CSE 82 85 87 91 85 430 86 \nB \n18 | P a g e\n\n3333 104 IT 99 82 84 89 100 \nA \n4444 108 IT 92 85 83 91 87 \nB \n5555 106 CSE 82 85 87 91 85 \nB \n3. DELETE \n \n//To delete a particular record whose the exam id is 2222 \n \nDELETE FROM exam WHERE examid=2222; \n \n \n//To display the records in table exam after deleted a record \n \nSELECT * FROM exam; \n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE  \nGRADE \n454 90.8 \n \n438 87.6 \n \n430 86 \n \n \n//To inserted the same record in table exam for further use \n \nINSERT INTO exam(examid, studentid, department, mark1, mark2, mark3, mark4,mark5,total,average,grade)  \nVALUES (2222,101,\'IT\',98,87,83,99,87, 454,90.8,‟A‟) \n \n//To display the updated values in the table exam \n \nSELECT * FROM exam; \n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE  \nGRADE \n2222 101 IT 98 87 83 99 87 454 	90.8 \nA \n3333 104 IT 99 82 84 89 100 454 90.8 \nA \n4444 108 IT 92 85 83 91 87 438 	87.6 \nB \n5555 106 CSE 	82 85 87 91 85 430 86 \nB \nResult: \nThus all the above DML SQL commands has been executed successfully and the output was verified. \n19 | P a g e', 'Sistema de gestión de bases de datos\nPráctica de laboratorio 1\n1 | P a g e\n\nEj.No: 1\nCOMANDOS BÁSICOS DE SQL\n \n//Crear tabla\nCREAR TABLA emp\n(\nempno NÚMERO\nempname VARCHAR2(255),\nFECHA DOB,\nsalario NÚMERO\ndesignación VARCHAR2(20)\n);\n \n \n// Insertar valores\nINSERT INTO emp VALUES(100,\'John\', \'21-Apr-1996\', 50000,\'Gerente\');\nINSERT INTO emp VALUES(101,\'Greg\', \'20-Julio-1994\', 2500,\'Empleado\');\n \n// Mostrar valores\nSELECT * FROM emp;\nEMPNO\n100\n101\nEMPNAME\nJohn\nGreg\nDOB\n21-abr-1996\n20-Jul-1994\nSALARIO\n50000\n2500\nDESIGNACIÓN\nGerente\nEmpleado\nSELECT empname,salario FROM emp;\nEMPNAME\nJohn\nGreg\nSALARIO\n50000\n25000\n2 | P á g i n a\n\n// Modificar valores\nUPDATE emp SET salario = salario + 1000;\n \n \nSELECT * FROM emp;\nEMPNO EMPNAME DOB SALARY DESIGNATION\n100 John 21-abr-1996 51000 Gerente\n101 Greg 20-Julio-1994 3500 Oficinista\n// Borrar valores\nDELETE FROM emp WHERE empno = 100;\n \n \nSELECT * FROM emp;\nEMPNO EMPNAME DOB SALARY DESIGNATION\n101 Greg 20-Julio- 1994 3500 Oficinista\nResultado:\nAsí pues, todos los comandos SQL básicos anteriores se han ejecutado correctamente y se ha verificado la salida.\n3 | P á g i n a\n\nEj.No: 2\nLenguaje de definición de datos (DDL)\nSentencias DDL\n \nCREATE TABLA\nALTER TABLA\nDROP TABLA\n \n1. Sentencia CREATE\nLa sentencia CREATE TABLE se utiliza para crear una tabla relacional\nCREAR TABLA nombre_tabla\n(\nnombre_columna1 tipo_datos [restricciones],\nnombre_columna1 tipo_datos [restricciones],\nnombre_columna1 tipo_datos [restricciones],\n........\n);\n \n2. Alterar tabla\n \nLa sentencia ALTER TABLE se utiliza para añadir, eliminar o modificar columnas en una tabla existente\n \na.Para añadir una columna\nALTER TABLE nombre_tabla ADD nombre_columna tipo_dato\nb. Para eliminar una columna de una tabla\nALTER TABLE nombre_tabla DROP COLUMN nombre_columna\nc. Para cambiar el tipo de datos de una columna en una tabla\nALTER TABLE nombre_tabla ALTER COLUMN nombre_columna tipo_datos\n \n3. Borrar tabla\nSe utiliza para eliminar la tabla permanentemente del almacenamiento\nDROP TABLA nombre_tabla\n4 | P á g i n a\n\nEj. No: 2.a\nLenguaje de definición de datos (DDL)\n(Sin restricción)\n1. CREAR LA TABLA\n \nCREAR TABLA emp\n(\nempno NÚMERO\nempname VARCHAR2(25),\ndob FECHA,\nsalario NÚMERO\ndesignación VARCHAR2(20)\n);\n \n- Tabla creada\n \n// Describir la tabla emp\n \nDESC emp;\nColumna Tipo de datos Longitud PrecisiónEscala Clave primaria Nulable Por defecto Comentario\nEMPNO NUMBER 22 - - - - -\nEMPNAME VARCHAR2 25 - - - - -\nDOB FECHA 7 - - - - -\nSALARIO NÚMERO 22 - - - -\nDESIGNACIÓN VARCHAR2 20 - - - - -\n2. MODIFIQUE LA TABLA\n \na.AGREGAR\n// Para alterar la tabla emp añadiendo el nuevo atributo departamento\nALTER TABLE emp ADD departamento VARCHAR2(50);\n5 | P á g i n a\n\nDESC emp;\nColumna Tipo de datos Longitud Precisión Escala Clave primaria Nulable Por defecto Comentario\nEMPNO NUMBER 22 - - - - -\nEMPNAME VARCHAR2 25 - - - - -\nDOB FECHA 7 - - - - -\nSALARIO NÚMERO 22 - - - -\nDESIGNACIÓN VARCHAR2 20 - - - - -\nDEPARTAMENTO VARCHAR2 50 - - - - -\nb. MODIFICAR\n// Para alterar la tabla emp modificando el tamaño del atributo departamento\n \nALTER TABLE emp MODIFY departamento VARCHAR2(100);\nDESC emp;\nColumna Tipo de datos Longitud Precisión Escala Clave primaria Nulable Por defecto Comentario\nEMPNO NUMBER 22 - - - - -\nEMPNAME VARCHAR2 25 - - - - -\nDOB FECHA 7 - - - - -\nSALARIO NÚMERO 22 - - - -\nDESIGNACIÓN VARCHAR2 20 - - - - -\nDEPARTAMENTO VARCHAR2 100 - - - - -\nc. DROP\n// Para modificar la tabla emp borrando el atributo departamento\n \nALTER TABLE emp DROP(departamento);\nLength PrecisionScale Primary Key Nullable Default Comment Data Type\nNÚMERO 22\nVARCHAR2 25\nFECHA 7\nNÚMERO 22\nDESC emp;\nColumna\nEMPNO\nEMPNAME\nDOB\nSALARIO\nDESIGNACIÓN VARCHAR2 20\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n-\n6 | P á g i n a\n\nd. RENOMBRAR\n// Para modificar el nombre de la tabla utilizando la palabra clave rename\n \nALTER TABLE emp RENAME TO emp1 ;\nDESC emp1;\nColumna Tipo de datos Longitud PrecisiónEscala Clave primaria Nulable Predeterminado Comentario\nEMPNO NUMBER 22 - - - - -\nEMPNAME VARCHAR2 25 - - - - -\nDOB FECHA 7 - - - - -\nSALARIO NÚMERO 22 - - - -\nDESIGNACIÓN VARCHAR2 20 - - - - -\nDEPARTAMENTO VARCHAR2 100 - - - - -\n3. DROP\n//Eliminar la tabla de la base de datos\n \nDROP TABLA emp1;\n \nDESC emp1;\nNo se ha podido encontrar el objeto a describir.\n7 | P á g i n a\n\nEj. Nº: 2.b\nLenguaje de definición de datos (DDL)\n(Con restricciones)\nTipos de restricciones\n \nNOT NULL\nUNIQUE\nPRIMARY CLAVE\nFOREIGN CLAVE\nCHECK\nDEFAULT\n1. CREE LA TABLA\n \n// Para crear una tabla alumno\n \nCREAR TABLA alumno\n(\nstudentID NÚMERO PRIMARY KEY,\nsname VARCHAR2(30) NOT NULL,\ndepartamento CHAR(5),\nsem NÚMERO\ndob FECHA,\nemail_id VARCHAR2(20) UNIQUE,\ncolegio VARCHAR2(20) DEFAULT \'MEC\'\n);\n \n// Describir la tabla student\n \nDESC estudiante;\nColumna Tipo de datos Longitud Precisión EscalaPrimaria ClaveNullableDefault Comentario\nSTUDENTID NÚMERO 22 - - 1 - - -\nSNAME VARCHAR2 30 - - - - -\nDEPARTMENT CHAR 5 - - - - -\nSEM NÚMERO 22 - - - -\nDOB FECHA 7 - - - - -\nEMAIL_ID VARCHAR2 20 - - - - -\nCOLLEGE VARCHAR2 20 - - - - \'MEC\' -\n8 | P a g e\n\n/Para crear una tabla examen\n \nCREAR TABLA examen\n(\nexamID NÚMERO ,\nstudentID NÚMERO REFERENCIAS student(studentID),\ndepartamento CHAR(5) NOT NULL\nmark1 NUMBER CHECK (mark1<=100 y mark1>=0),\nmark2 NÚMERO CHECK (mark2<=100 y mark2>=0),\nmark3 NÚMERO CHECK (mark3<=100 y mark3>=0),\nmarca4 NÚMERO VERIFICADO (marca4<=100 y marca4>=0),\nmarca5 NÚMERO VERIFICADO (marca5<=100 y marca5>=0),\ntotal NÚMERO\npromedio NÚMERO,\ncalificación CHAR(1)\n);\n \n \n//Describir la tabla examen\nDESC examen\nColumna Tipo de datos Longitud Precisión Escala Clave primaria Nulable Por defecto Comentario\nEXAMID NÚMERO 22 - - - - -\nSTUDENTID NÚMERO 22 - - - - -\nDEPARTMENT CHAR 5 - - - - -\nMARK1 NÚMERO 22 - - - - -\nMARK2 NÚMERO 22 - - - -\nMARK3 NÚMERO 22 - - - -\nMARK4 NÚMERO 22 - - - -\nMARK5 NÚMERO 22 - - - -\nTOTAL NÚMERO 22 - - - - -\nNÚMERO MEDIO 22 - - - - -\nNOTA CHAR 1 - - - - -\n2. ALTERAR LA TABLA\n \nA. AÑADIR\n//Alterar la tabla alumno añadiendo un nuevo atributo dirección\n \nALTER TABLE estudiante ADD dirección VARCHAR2(100);\n9 | P á g i n a\n\nMANUAL DE LABORATORIO DBMS, Departamento de Tecnología de la Información, SMVEC.\nDESC estudiante;\nColumna Tipo de datos Longitud Precisión EscalaClave primaria NullableDefault Comentario\nSTUDENTID NÚMERO 22 - - 1 - - -\nSNAME VARCHAR2 30 - - - - -\nDEPARTMENT CHAR 5 - - - - -\nSEM NÚMERO 22 - - - -\nDOB FECHA 7 - - - - -\nEMAIL_ID VARCHAR2 20 - - - - -\nCOLLEGE VARCHAR2 20 - - - - \'MEC\' -\nADDRESS VARCHAR2 100 - - - - -\n//Actuar sobre la tabla student añadiendo una nueva restricción al atributo examID\n \nALTER TABLE examen ADD CONSTRAINT pr PRIMARY KEY (examID);\n \nB. MODIFICAR\n/Alterar la tabla student modificando el tamaño del atributo address\nALTER TABLE student MODIFY dirección VARCHAR2(150);\nDESC estudiante;\nColumna Tipo de datos Longitud Precisión Escala Clave primaria Nulable Por defecto Comentario\nSTUDENTID NÚMERO 22 - - 1 - -\nSNAME VARCHAR2 30 - - - - -\nDEPARTMENT CHAR 5 - - - - -\nSEM NÚMERO 22 - - - -\nDOB FECHA 7 - - - - -\nEMAIL_ID VARCHAR2 20 - - - - -\nCOLLEGE VARCHAR2 20 - - - - \'MEC\' -\nDIRECCIÓN\n \nC. DROP\nVARCHAR2 150 - - - - -\n//Actuar sobre la tabla alumno suprimiendo el atributo dirección\n \nALTER TABLE estudiante DROP(dirección);\n10 | P á g i n a\n\nDESC alumno;\nColumna Tipo de datos Longitud\n-\n-\n-\n-\n-\n-\nPrecisión EscalaPrimario ClaveNullableDefault Comentario\n-\n-\n-\n-\n-\n-\nSTUDENTID NÚMERO 22 - - 1 -\nSNAME VARCHAR2 30 - - -\nDEPARTMENT CHAR 5 - - -\nSEM NÚMERO 22 - - -\nDOB FECHA 7 - - -\nEMAIL_ID VARCHAR2 20 - - -\nCOLLEGE VARCHAR2 20 - - - - \'MEC\' -\nD. RENAME\n// Para modificar el nombre de la tabla utilizando la palabra clave rename\n \nALTER TABLE student RENAME A student1 ;\nTabla alterada\nDESC estudiante1;\n \nColumna\n \nTipo de datos\n \nLongitud\n \nPrecisión EscalaPrimario ClaveNulablePor defecto Comentario\nSTUDENTID NÚMERO 22 - - 1 - - -\nSNAME VARCHAR2 30 - - - - -\nDEPARTMENT CHAR 5 - - - - -\nSEM NÚMERO 22 - - - -\nDOB FECHA 7 - - - - -\nEMAIL_ID VARCHAR2 20 - - - - -\nCOLLEGE VARCHAR2 20 - - - - \'MEC\' -\nALTER TABLE student1 RENAME TO student ;\nTabla modificada\n \n \n3. DROP\n \n// Para eliminar la tabla de la base de datos\n \nDROP TABLA exam;\n \n \nDESC examen;\nNo se ha podido encontrar el objeto a describir.\n \n \nResultado:\nAsí pues, todos los comandos SQL DDL anteriores se han ejecutado correctamente y se ha verificado la salida.\n11 | P á g i n a\n\nRestricción SQL FOREIGN KEY\n \nUna CLAVE FOREÑA es una clave utilizada para vincular dos tablas entre sí.\nUna CLAVE FOREÑA es un campo (o colección de campos) de una tabla que hace referencia a la\nCLAVE PRIMARIA en otra tabla.\nLa tabla que contiene la clave foránea se denomina tabla hija, y la tabla\nque contiene la clave candidata se denomina tabla referenciada o matriz.\n \nEjemplo\nVea las dos tablas siguientes:\n \n \n \n \n \n \n \n \n \n \n \n \nObserve que la columna \"PersonID\" de la tabla \"Pedidos\" apunta a la columna\ncolumna \"PersonID\" de la tabla \"Persons\".\nLa columna \"PersonID\" de la tabla \"Persons\" es la CLAVE PRIMARIA de la tabla\ntabla \"Personas\".\nLa columna \"PersonID\" de la tabla \"Orders\" es una FOREIGN KEY en la tabla \"Orders\"\ntabla \"Pedidos\".\nLa restricción FOREIGN KEY se utiliza para evitar acciones que destruirían vínculos\nentre tablas.\nLa restricción FOREIGN KEY también impide que se inserten datos no válidos en\nla columna de clave foránea, porque tiene que ser uno de los valores contenidos en la\ntabla a la que apunta.\n\nSQL FOREIGN KEY en CREAR TABLA\nEl siguiente SQL crea una CLAVE EXTRAÑA en la columna \"PersonID\" cuando\nse crea la tabla \"Pedidos\":\n \nCREAR TABLA Pedidos (\n    OrderID int NOT NULL PRIMARY KEY,\n    OrderNumber int NOT NULL\n    PersonID int FOREIGN KEY REFERENCIAS Persons(PersonID)\n);\n \nSQL FOREIGN KEY en ALTER TABLE\n \nPara crear una restricción FOREIGN KEY en la columna \"PersonID\" cuando la tabla\ntabla \"Pedidos\" ya está creada, utilice el siguiente SQL:\n \nALTER TABLE Pedidos\nADD CONSTRAINT FK_PersonOrder\nFOREIGN KEY (PersonID) REFERENCES Persons(PersonID);\n \nDROP una restricción FOREIGN KEY\nPara eliminar una restricción FOREIGN KEY, utilice el siguiente SQL:\n \nALTER TABLE Pedidos\nDROP CONSTRAINT FK_PersonOrder;\n\nEj.No: 3\nLENGUAJE DE MANIPULACIÓN DE DATOS (DML)\n \nSentencias DML\n \nInsert into\nUpdate\nDelete\n \n1. Inserte en\n \nLa sentencia INSERT INTO se utiliza para insertar una nueva fila en una tabla.\n \nDiferentes formas de insertar un nuevo registro en la tabla\n \na. Sustitución directa\n \nINSERT INTO nombre_tabla VALUES(valor1, valor2,valor3, ..........);\n \nb.Inserción de columna específica\n \nINSERT INTO nombre_tabla (columna1, columna2, columna3, .....)\nVALUES (valor1, valor2,valor3, .........) ;\n \nc.Sustitución de macros, esta consulta se utiliza para recibir valores en tiempo de ejecución.\n \nINSERT INTO nombre_tabla VALUES(&nombrecolumna1,&nombrecolumna2, ....);\n \n2. Actualizar\n \nActualizar nuevos datos en una tabla existente\n \nSintaxis\n \nUPDATE nombre_tabla SET columna1=valor, columna2=valor2,... WHERE columna=valor;\n \n3. Borrar consulta\n \nLa consulta DELETE se utiliza para eliminar filas de una tabla.\n \nSintaxis\n \nDELETE FROM nombre_tabla WHERE columna1=algún_valor;\n14 | P á g i n a\n\n1. INSERTAR\n// Para insertar los valores como filas en la tabla alumno\nSustitución directa\nINSERT INTO alumno VALUES(101,\'RUPESH\',\'IT\',5,\'18/04/1996\',\'rupesh@gmail.com\',\'MEC\');\nINSERT INTO student VALUES (102,\'BALA\',\'CSE\',7,\'10/7/1995\',\'bala@gmail.com\',\'IIT\');\nINSERT INTO student VALUES (104,\'HEMESH\',\'IT\',5,\'7/23/1996\',\'hemesh@gmail.com\',\'IIT\');\n \nINSERT INTO student VALUES (106,\'SAIVAISHNAVI\',\'CSE\',5,\'06/9/1996\',\'vaishu@gmail.com\',\'IFET\');\n \nINSERT INTO student(studentid,sname,department,sem,dob,email_id)\nVALUES (108,\'RISHA\',\'IT\',5,\'04/21/1996\',\'risha@gmail.com\'); //(A efectos de restricción por defecto-\nInserción de columna específica)\n// Para mostrar todos los registros de la tabla alumno\n \nSELECT * FROM estudiante;\nSTUDENTID\nCOLEGIO\nSNAME DEPARTMENT SEM DOB EM AIL_ID\n101 RUPESH IT 5 18/04/1996 rupesh@gmail.com MEC\n102 BALA CSE 7 10/07/1995 bala@gmail.com IIT\n104 HEMESH IT 5 23/07/1996 hemesh@gmail.com IIT\n106 SAI VAISHNAVI CSE 5 06/09/1996 vaishu@gmail.com SMVEC\n108 RISHA IT 5 21/04/1996 risha@gmail.com MEC\n// Para insertar los valores como filas en el examen de la tabla\n \nInserción de columnas específicas\n \nINSERT INTO examen(examid, studentid, departamento, nota1, nota2, nota3, nota4, nota5)\nVALUES (2222,101,\'IT\',98,87,83,99,87);\n \nINSERT INTO examen(examid, studentid, department, mark1, mark2, mark3, mark4,mark5)\nVALUES(3333,104,\'IT\',99,82,84,89,100);\n \nINSERT INTO examen(examid, studentid, department, mark1, mark2, mark3, mark4,mark5)\nVALUES(4444,108,\'IT\',92,85,83,91,87);\n15 | P á g i n a\n\nSustitución de macros\n \n \nINSERT INTO examen(&examid, &studentid, &department, &mark1, &mark2, &mark3,&mark4,&mark5)\nVALUES(5555,106,\'CSE\',82,85,87,91,85);\n \nIntroduzca el valor para examid : 5555\nIntroduzca el valor para studentid 106\nIntroduzca el valor para department: CSE\nIntroduzca el valor de la nota1: 82\nIntroduzca el valor para mark2 85\nIntroduzca el valor de la nota3: 87\nIntroduzca el valor de la nota4: 91\nIntroduzca el valor de la marca5: 85\n \nold 1: INSERT INTO examen VALUES(&examid); INSERT INTO examen\nVALUES(5555,106,‟CSE‟,82,85,87,91,85)\nnuevo 1: INSERT INTO exam VALUES(5555,106, \"CSE\",82,85,87,91,85)\n \n \n// Para visualizar todos los registros de la tabla examen\n \nSELECT * FROM examen;\nNOTA5 TOTAL MEDIA EXAMID STUDENTID DEPARTAMENTO NOTA1 NOTA2 NOTA3 NOTA4\nCALIFICACIÓN\n2222\n-\n101 IT 98 87 83 99 87 - - -\n3333 104 IT 99 82 84 89 100 - - -\n-\n4444 108 IT 92 85 83 91 87 - - -\n-\n5555 106 CSE 82 85 87 91 85 - - -\n-\n2. ACTUALIZACIÓN\n \n// Para cambiar los valores de la tabla alumno\n \nUPDATE student SET college=\'MEC\' WHERE studentid=108;\n1 fila(s) actualizada(s)\n16 | P á g i n a\n\n// Para mostrar el valor actualizado en la tabla student\n \nSELECT * FROM estudiante;\nSTUDENTID SNAME DEPARTMENT SEM DOB EMAIL_ID COLLEGE\n101 RUPESH IT 5 18/04/1996 rupesh@gmail.com MEC\n102 BALA CSE 7 10/07/1995 bala@gmail.com IIT\n104 HEMESH IT 5 23/07/1996 hemesh@gmail.com IIT\n106 SAI VAISHNAVI CSE 5 06/09/1996 vaishu@gmail.com SMVEC\n108 RISHA IT 5 21/04/1996 risha@gmail.com MEC\n//Para fijar el total en el examen de la tabla\n \nUPDATE examen SET total=(marca1+marca2+marca3+marca4+marca5);\n4 fila(s) actualizada(s)\n \n//Para mostrar el valor actualizado en la tabla examen\n \nSELECT * FROM examen;\nNOTA5 TOTAL MEDIA EXAMID STUDENTID DEPARTAMENTO NOTA1 NOTA2 NOTA3 NOTA4\nCALIFICACIÓN\n2222\n-\n101 IT 98 87 83 99 87 454 - -\n3333 104 IT 99 82 84 89 100 454 - -\n-\n4444 108 IT 92 85 83 91 87 438 - -\n-\n5555 106 CSE 82 85 87 91 85 430 - -\n-\n//Para establecer la media en el examen de la tabla\n \nUPDATE examen SET media=total/5;\n17 | P á g i n a\n\nMANUAL DE LABORATORIO DBMS, Departamento de Tecnología de la Información, SMVEC.\n//Para mostrar el valor actualizado en la tabla exam\n \nSELECT * FROM examen;\n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE\nCALIFICACIÓN\n2222\n-\n101 IT\n-\n98 87 83 99 87 454 90.8 -\n3333 104 IT 99 82 84 89 100 454 90,8 -\n- -\n4444 108 IT 92 85 83 91 87 438 87.6 -\n- -\n5555 106 CSE 82 85 87 91 85 430 86 -\n- -\n/Para fijar la nota en el examen de mesa\n \nUPDATE exam SET grade=\'S\' WHERE average>95;\nUPDATE exam SET grade=\'A\' WHERE average<=95 AND average>90;\nUPDATE exam SET grade=\'B\' WHERE average<=90 AND average>85\nUPDATE exam SET grade=\'C\' WHERE average<=85 AND average>80;\nUPDATE exam SET grade=\'D\' WHERE average<=80 AND average>75\nUPDATE exam SET grade=\'F\' WHERE average<75;\n \n \n/Para mostrar los valores actualizados en la tabla examen\n \nSELECT * FROM examen;\n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE\nGRADO\n2222 101 IT 98 87 83 99 87 454 90,8\nA\n3333 104 IT 99 82 84 89 100 454 90.8\nA\n4444 108 IT 92 85 83 91 87 438 87,6\nB\n5555 106 CSE 82 85 87 91 85 430 86\nB\n18 | P á g i n a\n\n3333 104 IT 99 82 84 89 100\nA\n4444 108 IT 92 85 83 91 87\nB\n5555 106 CSE 82 85 87 91 85\nB\n3. BORRAR\n \n/Para borrar un registro concreto cuyo id de examen es 2222\n \nDELETE FROM examen WHERE examid=2222;\n \n \n//Para mostrar los registros de la tabla examen después de eliminar un registro\n \nSELECT * FROM examen;\n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE\nGRADO\n454 90.8\n \n438 87.6\n \n430 86\n \n \n//Para insertar el mismo registro en la tabla examen para su uso posterior\n \nINSERT INTO examen(examid, studentid, departamento, nota1, nota2, nota3, nota4,nota5,total,media,nota)\nVALORES (2222,101,\'IT\',98,87,83,99,87, 454,90.8, \"A\")\n \n//Mostrar los valores actualizados en la tabla examen\n \nSELECT * FROM examen;\n \nEXAMID STUDENTID DEPARTMENT MARK1 MARK2 MARK3 MARK4 MARK5 TOTAL AVERAGE\nGRADO\n2222 101 IT 98 87 83 99 87 454 90,8\nA\n3333 104 IT 99 82 84 89 100 454 90.8\nA\n4444 108 IT 92 85 83 91 87 438 87,6\nB\n5555 106 CSE 82 85 87 91 85 430 86\nB\nResultado:\nAsí pues, todos los comandos SQL DML anteriores se han ejecutado correctamente y se ha verificado la salida.\n19 | P á g i n a', 'In Progress', '2024-10-07 00:39:09', '2024-10-07 00:59:44'),
(6, 2, 'English', 'Spanish', 'OBI JOSEPH\nContact Address: No. 67 Isaiah Eletuo Street Oyigbo, Rivers State\nEmail: \nTel: 08032747698, 08128533116\nPERSONAL DATA:\nSex: 					Male\nDate of Birth: 				25th May, 1975\nMarital Status: 			Married\nLocal Govt. Area: 			Ukwa West\nState of Origin: 			Abia\nReligion: 				Christianity\nACADEMIC QUALIFICATION WITH DATES\nS/N INSTITUTION CERTIFICATE OBTAINED DATE \n1 Community Primary School Aba, Abia State First School Leaving Certificate (FSLC) 1981 -1986 \n2 Government College, Umuahia, Abia State West African Examination Council (WAEC) 1987-1994 \nWORK EXPERIENCE\nS/N COMPANY POSITION YEAR \n1 Deawoo Nig. Ltd Bonny Island DN24 Iron Worker 1998 - 2000 \n2 Chicago Bridge &amp; Iron (CB&amp;I) Nig. Ltd Bonny Island Mechanical Fitter 2001 - 2003 \n3 Hyyundai Heavy Industry Nig. Ltd (HHI) Bonny Island Iron Worker 2004 - 2006 \n4 Deawoo Nig. Ltd Afam IV Integrated Power Plant Project, Oyigbo (DN 52) Rivers State Structural Fitter 2006 - 2009 \n5 Deawoo Nig. Ltd DN 59 Excaravus Gas to Liquid, Warri Structural Fitter 2010 - 2012 \n6 Samsung Heavy Industrial Nig. Shin Pipe Fitter 2016 - 2018 \n7 Deawoo Nig. Ltd DN64 Indorama Structural Fitter 2018 - 2021 \nAll with Certificate \nHOBBIES:\nReading and Music\nREFEREES:\nPst. Eng. Chris Onwugbufor						Prof. Chibuike Kanu Ph.D\nDBN Nig. Bonny Island							Commissioner of Education\n08033921497, 08078793189						08067967775', 'OBI JOSEPH\nDirección de contacto: No. 67 Isaiah Eletuo Street Oyigbo, Estado de Rivers\nCorreo electrónico:\nTel: 08032747698, 08128533116\nDATOS PERSONALES\nSexo: Masculino\nFecha de nacimiento: 25 de mayo de 1975\nEstado civil: Casado\nÁrea de gobierno local: Ukwa West\nEstado de origen: Abia\nReligión: Cristianismo\nTITULACIÓN ACADÉMICA CON FECHAS\nS/N INSTITUCIÓN CERTIFICADO OBTENIDO FECHA\n1 Community Primary School Aba, Estado de Abia First School Leaving Certificate (FSLC) 1981 -1986\n2 Government College, Umuahia, Estado de Abia West African Examination Council (WAEC) 1987-1994\nEXPERIENCIA LABORAL\nS/N EMPRESA CARGO AÑO\n1 Deawoo Nig. Ltd Bonny Island DN24 Iron Worker 1998 - 2000\n2 Chicago Bridge &amp; Iron (CB&amp;I) Nig. Ltd Bonny Island Montador mecánico 2001 - 2003\n3 Hyyundai Heavy Industry Nig. Ltd (HHI) Bonny Island Trabajador del hierro 2004 - 2006\n4 Deawoo Nig. Ltd Proyecto de central eléctrica integrada Afam IV, Oyigbo (DN 52) Estado de Rivers Montador estructural 2006 - 2009\n5 Deawoo Nig. Ltd DN 59 Excaravus Gas to Liquid, Warri Montador estructural 2010 - 2012\n6 Samsung Heavy Industrial Nig. Shin Instalador de tuberías 2016 - 2018\n7 Deawoo Nig. Ltd DN64 Indorama Instalador estructural 2018 - 2021\nTodos con Certificado\nAFICIONES\nLa lectura y la música\nREFERENTES:\nPst. Eng. Chris Onwugbufor Prof. Chibuike Kanu Ph.D\nDBN Nig. Comisario de Educación de la Isla de Bonny\n08033921497, 08078793189 08067967775', 'Pending', '2024-10-07 04:24:25', '2024-10-07 04:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `translation_engines`
--

CREATE TABLE `translation_engines` (
  `id` int(11) NOT NULL,
  `engine_name` varchar(100) NOT NULL,
  `api_endpoint` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `translation_logs`
--

CREATE TABLE `translation_logs` (
  `id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `action` enum('Created','Reviewed','Updated','Deleted') NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `translation_logs`
--

INSERT INTO `translation_logs` (`id`, `translation_id`, `action`, `user_id`, `created_at`) VALUES
(5, 5, 'Created', 4, '2024-10-07 00:39:09'),
(6, 6, 'Created', 4, '2024-10-07 04:24:25');

-- --------------------------------------------------------

--
-- Table structure for table `translation_reviews`
--

CREATE TABLE `translation_reviews` (
  `id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `feedback` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `translation_reviews`
--

INSERT INTO `translation_reviews` (`id`, `translation_id`, `reviewer_id`, `feedback`, `rating`, `created_at`, `updated_at`) VALUES
(1, 5, 5, NULL, NULL, '2024-10-07 00:59:43', '2024-10-07 00:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User','Reviewer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', 'Admin', 'Admin', '2024-10-01 02:11:41', '2024-10-01 02:11:41'),
(4, 'Jedidiah01', 'victorfidelisjedidiah@gmail.com', '123456', 'User', '2024-10-01 14:35:57', '2024-10-01 14:35:57'),
(5, 'Jedidiah', 'victor@gmail.com', '12345', 'Reviewer', '2024-10-02 00:03:08', '2024-10-02 00:03:08'),
(6, 'Marcus', 'chibegerichard1200@gmail.com', '12345', 'User', '2024-10-02 05:16:58', '2024-10-02 05:16:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `language_pairs`
--
ALTER TABLE `language_pairs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `translation_engines`
--
ALTER TABLE `translation_engines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translation_logs`
--
ALTER TABLE `translation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translation_id` (`translation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `translation_reviews`
--
ALTER TABLE `translation_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translation_id` (`translation_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `language_pairs`
--
ALTER TABLE `language_pairs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `translation_engines`
--
ALTER TABLE `translation_engines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `translation_logs`
--
ALTER TABLE `translation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `translation_reviews`
--
ALTER TABLE `translation_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `translations_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `translation_logs`
--
ALTER TABLE `translation_logs`
  ADD CONSTRAINT `translation_logs_ibfk_1` FOREIGN KEY (`translation_id`) REFERENCES `translations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `translation_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `translation_reviews`
--
ALTER TABLE `translation_reviews`
  ADD CONSTRAINT `translation_reviews_ibfk_1` FOREIGN KEY (`translation_id`) REFERENCES `translations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `translation_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
