# Gate App – Staff and Visitor Management System

**Gate App** is a web-based staff and visitor management system designed to help organisations manage people entering and leaving their premises.

Originally suited to educational environments, Gate App can be configured for schools, colleges, offices, commercial buildings, and other organisations. It provides a centralised way to record arrivals and departures, monitor who is on site, and support reception and administrative activities.

The system is designed to be adaptable, allowing organisations to configure locations, visitor details, staff information, and other settings to suit their operational requirements.

## Features

- **Staff check-in and check-out:** Record staff arrival and departure times.
- **Visitor check-in and check-out:** Record visitor details and track visits.
- **On-site monitoring:** Allow authorised reception staff to view people currently on site.
- **Visitor badge printing:** Support visitor badge printing using a compatible label printer and configured templates.
- **Reception management:** Allow authorised users to manage visitor records and complete check-outs when required.
- **Staff and visitor records:** Maintain records of arrivals and departures for operational purposes.
- **Search and reporting:** Enable administrators to search records and review visit information, where supported by the application.
- **Multiple locations:** Support configuration for organisations with multiple sites, subject to application settings.
- **Customisable configuration:** Adapt fields, locations, branding, and other settings for different organisations.
- **Education-friendly design:** Suitable for school reception areas, staff attendance recording, parent and contractor visits, and other authorised site visits.
- **Business adaptability:** Can be configured for general office, corporate, and commercial environments.
- **Touchscreen support:** Retain virtual keyboard functionality where supported and enabled in the existing application.

## Intended Use

Gate App can support the following environments:

- Schools and colleges
- Nurseries and educational facilities
- Corporate offices
- Reception and front-desk environments
- Commercial premises
- Multi-site organisations

Organisations should configure the application according to their visitor procedures, staff attendance requirements, privacy obligations, and security policies.

## System Requirements

### Server Requirements

The original application was designed for the following server-side technologies:

- Apache 2.2 or 2.4 and compatible web servers
- PHP 5.6 or PHP 7 and later compatible versions
- MySQL 5.5, MySQL 5.7, or compatible versions

**Important:** These are the historical requirements of the original application, not a recommendation to deploy an old PHP or MySQL version today. Before deployment, assess and update the application for a currently supported PHP version, database version, and security configuration.

### Client Requirements

- A modern web browser
- HTML5 support
- JavaScript enabled
- Network access to the application

### Badge Printing Requirements

If using the original badge-printing functionality:

- A compatible DYMO LabelWriter printer
- Compatible labels, such as DYMO 99012, when using the existing template
- An appropriate printer-sharing or printing configuration

Badge templates and printer settings may need to be adapted for other printers and label sizes.

## Installation

1. Extract the application files into the web server's application directory.
2. Configure the application settings in `settings.php`, using the existing configuration options.
3. Replace the default badge logo with your organisation's logo, if badge printing is required.
4. Configure database connection details and create the required database and database user.
5. Review web server access controls and protect administration and reception pages.
6. Configure badge templates, languages, locations, and other available options.
7. Run the supplied installation script, if supported by the version being deployed, or create the database tables manually.
8. Test staff and visitor workflows, access permissions, check-in/check-out records, and printing before putting the application into production.

## Database Setup

The original application uses MySQL and includes a visitor records table.

Example database creation statement:

```sql
CREATE DATABASE IF NOT EXISTS visitors
CHARACTER SET utf8
COLLATE utf8_unicode_ci;
```

The original visitor table contains fields for visitor identity, location, organisation, visit details, vehicle information, legal acknowledgements, and arrival/departure times.

The database structure must be reviewed and extended if Gate App is to support dedicated staff accounts, authenticated staff attendance, role-based access, or additional reporting.

## Security and Access Control

Gate App should be deployed with appropriate security controls, including:

- Authentication for staff and administrative users.
- Role-based permissions for receptionists and administrators.
- Secure password storage and session handling.
- Input validation and protection against SQL injection.
- HTTPS for all web access.
- Restricted access to administrative pages.
- Appropriate database permissions and regular backups.
- Defined data retention and privacy procedures.

The original application documentation describes separate database permissions for visitor-facing, reception, and administrative functions. These permissions should be reviewed against the actual implementation before deployment.

## Configuration and Customisation

Gate App is intended to be adaptable to different organisational requirements.

Depending on the functionality implemented, customisation may include:

- Organisation name and logo
- Site and building locations
- Staff and visitor categories
- Visitor and staff check-in/check-out fields
- Badge layouts and printing settings
- Languages and interface text
- Administrative access and reporting
- Integration with other systems

Any additional staff-management, authentication, or integration features must be implemented and tested before being presented as available functionality.

## Third-Party Software and Licensing

Gate App incorporates or is derived from an existing open-source visitor-management application.

The applicable third-party copyright notices, attribution requirements, Apache License 2.0 terms, and notices for bundled libraries must be retained in accordance with their respective licences.

Where applicable, refer to the accompanying `NOTICE` file for information about third-party components.

Apache License 2.0:

http://www.apache.org/licenses/LICENSE-2.0

The software is provided on an "AS IS" basis, without warranties or conditions of any kind, as specified by the applicable licence.

## Disclaimer

Gate App is intended to assist with staff and visitor record-keeping. It does not, by itself, guarantee physical security, regulatory compliance, or accurate attendance records. Organisations are responsible for configuring, securing, testing, and operating the system appropriately.

## About Gate App

Gate App provides a foundation for managing staff and visitor movements within an organisation. Its configuration can be adapted to educational and commercial environments, with further development available to meet specific operational requirements.
