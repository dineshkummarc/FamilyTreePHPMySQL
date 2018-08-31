SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `tblfamily`
--

CREATE TABLE `tblfamily` (
  `idfamily` int(11) NOT NULL,
  `familyname` varchar(100) DEFAULT NULL,
  `familyalias` varchar(200) DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblperson`
--

CREATE TABLE `tblperson` (
  `idperson` int(11) NOT NULL,
  `idfamily` int(11) NOT NULL DEFAULT '1',
  `personname` varchar(200) NOT NULL,
  `nickname` varchar(200) NOT NULL,
  `dateofbirth` date DEFAULT NULL,
  `dateofdeath` date DEFAULT NULL,
  `gender` varchar(10) NOT NULL DEFAULT 'Male',
  `profilepic` varchar(500) DEFAULT 'facebook-avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblp_to_p_link`
--

CREATE TABLE `tblp_to_p_link` (
  `idp_to_p_link` int(11) NOT NULL,
  `idperson_from` int(11) NOT NULL,
  `idperson_to` int(11) NOT NULL,
  `idrelation` int(11) NOT NULL,
  `relationdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblrelation`
--

CREATE TABLE `tblrelation` (
  `idrelation` int(11) NOT NULL,
  `relationname` varchar(100) NOT NULL,
  `representation` set('solid','dotted','dashed','') NOT NULL DEFAULT 'solid',
  `isactive` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblrelation`
--

INSERT INTO `tblrelation` (`idrelation`, `relationname`, `representation`, `isactive`) VALUES
(1, 'Child', 'solid', 1),
(2, 'Married', 'solid', 1),
(3, 'Divorced', 'dotted', 1),
(4, 'Adopted', 'dashed', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblfamily`
--
ALTER TABLE `tblfamily`
  ADD PRIMARY KEY (`idfamily`);

--
-- Indexes for table `tblperson`
--
ALTER TABLE `tblperson`
  ADD PRIMARY KEY (`idperson`);

--
-- Indexes for table `tblp_to_p_link`
--
ALTER TABLE `tblp_to_p_link`
  ADD PRIMARY KEY (`idp_to_p_link`);

--
-- Indexes for table `tblrelation`
--
ALTER TABLE `tblrelation`
  ADD PRIMARY KEY (`idrelation`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblfamily`
--
ALTER TABLE `tblfamily`
  MODIFY `idfamily` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tblperson`
--
ALTER TABLE `tblperson`
  MODIFY `idperson` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tblp_to_p_link`
--
ALTER TABLE `tblp_to_p_link`
  MODIFY `idp_to_p_link` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tblrelation`
--
ALTER TABLE `tblrelation`
  MODIFY `idrelation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
