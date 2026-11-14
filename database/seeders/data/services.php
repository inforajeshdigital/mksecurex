<?php

// Original starter content for the 7 core services. Everything is editable in Admin → Services.
$process = [
    ['title' => 'Requirement Study', 'text' => 'We discuss roles, headcount, site conditions, shift timings and service expectations with your team.'],
    ['title' => 'Plan & Proposal', 'text' => 'You receive a clear deployment plan with role profiles, schedules and a documented scope of work.'],
    ['title' => 'Deployment', 'text' => 'Screened personnel are onboarded and deployed, with a supervisor coordinating the handover at your site.'],
    ['title' => 'Monitoring & Support', 'text' => 'Attendance, performance and feedback are reviewed regularly, with quick replacements when needed.'],
];

return [
    [
        'title' => 'Security Services', 'slug' => 'security-services', 'icon' => 'fa-user-shield',
        'short' => 'Trained security guards and supervisors for access control, patrolling and asset protection at offices, plants, warehouses and campuses.',
        'intro' => 'Security is at the heart of MK Securex. We deploy trained, uniformed security personnel who protect your people, premises and assets — with clear post instructions, supervision and reporting.',
        'desc' => '<p>Every site has different risks. A corporate office needs courteous front-desk access control, a manufacturing plant needs material-gate checks and fire-round vigilance, and a construction site needs night patrolling of tools and stock. Our security services start with understanding these risks and then assigning the right mix of guards, supervisors and duty schedules.</p><p>Personnel are briefed on site-specific standing orders before deployment. Supervisors carry out surprise checks, verify registers and keep your administration team informed through a single point of contact.</p>',
        'features' => [
            ['title' => 'Access Control', 'text' => 'Visitor management, ID verification, gate passes and entry/exit registers.'],
            ['title' => 'Patrolling & Night Rounds', 'text' => 'Scheduled perimeter rounds with checkpoint logs for 24×7 sites.'],
            ['title' => 'Material Movement Checks', 'text' => 'Inward/outward verification of goods, vehicles and returnable items.'],
            ['title' => 'Event & Reception Security', 'text' => 'Courteous, well-groomed guards for lobbies, events and VIP movement.'],
        ],
        'benefits' => [
            ['title' => 'Deterrence', 'text' => 'Visible, alert security reduces the risk of theft and unauthorised entry.'],
            ['title' => 'Accountability', 'text' => 'Duty rosters, registers and incident reports keep every shift accountable.'],
            ['title' => 'Supervised Teams', 'text' => 'Field supervisors check posts and handle replacements quickly.'],
            ['title' => 'Flexible Strength', 'text' => 'Increase guard strength for peak periods, events or new sites.'],
        ],
        'faqs' => [
            ['q' => 'Do you provide security guards for 24×7 sites?', 'a' => 'Yes. We plan rotational shifts with relief staff so posts remain covered round the clock, including weekends and holidays.'],
            ['q' => 'How are guards briefed for our site?', 'a' => 'Before deployment we prepare site-specific post instructions with your team, covering access rules, emergency contacts and reporting formats.'],
            ['q' => 'Can we request a replacement guard?', 'a' => 'Yes. Share your feedback with your account coordinator and a suitable replacement will be arranged as quickly as possible.'],
        ],
    ],
    [
        'title' => 'Manpower Supply Services', 'slug' => 'manpower-supply-services', 'icon' => 'fa-people-group',
        'short' => 'Skilled, semi-skilled and support staff sourced, screened and deployed to match your roles, shifts and headcount needs.',
        'intro' => 'Our manpower supply services help you fill operational roles without the time and effort of recruiting, onboarding and managing a large support workforce on your own.',
        'desc' => '<p>From office assistants and data-entry operators to production helpers and drivers, we source candidates against the role profile you share. Each candidate is screened for experience and document verification before being presented or deployed.</p><p>You can engage staff for a short project, a seasonal peak or a long-term outsourced arrangement. We manage attendance records, replacements and statutory paperwork, so your managers can focus on the work rather than the administration.</p>',
        'features' => [
            ['title' => 'Role-Based Sourcing', 'text' => 'Candidates matched to skills, experience and shift requirements.'],
            ['title' => 'Document Verification', 'text' => 'Identity and address documents checked before deployment.'],
            ['title' => 'Temporary & Long-Term', 'text' => 'Short projects, seasonal peaks or ongoing contracts.'],
            ['title' => 'Replacement Support', 'text' => 'Quick replacements for absenteeism or attrition.'],
        ],
        'benefits' => [
            ['title' => 'Faster Hiring', 'text' => 'Reduce the time it takes to fill operational roles.'],
            ['title' => 'Lower Admin Load', 'text' => 'Attendance and paperwork handled by our team.'],
            ['title' => 'Scalable Headcount', 'text' => 'Scale up or down as your workload changes.'],
            ['title' => 'Single Point of Contact', 'text' => 'One coordinator for requests and escalations.'],
        ],
        'faqs' => [
            ['q' => 'What kind of roles can you supply?', 'a' => 'We supply skilled, semi-skilled and unskilled support roles such as office assistants, helpers, operators, drivers, data-entry staff and more, based on your requirement.'],
            ['q' => 'What is the minimum engagement period?', 'a' => 'Engagements can be short-term or long-term. Share your timeline and we will propose a suitable arrangement.'],
        ],
    ],
    [
        'title' => 'Facility Management Services', 'slug' => 'facility-management-services', 'icon' => 'fa-building-shield',
        'short' => 'Integrated upkeep of your premises — soft services, maintenance coordination and on-site supervision under one plan.',
        'intro' => 'Our facility management services bring housekeeping, front-office support, pantry, maintenance coordination and security under one managed plan with a single accountable team.',
        'desc' => '<p>Managing several vendors for cleaning, maintenance, pantry and security often leads to gaps in accountability. We simplify this by offering an integrated facility team, led by an on-site supervisor who follows a documented daily, weekly and monthly checklist.</p><p>We coordinate routine upkeep such as electrical, plumbing and minor repairs with your approved technicians, track consumables and share periodic reports, so your premises stay clean, safe and fully functional.</p>',
        'features' => [
            ['title' => 'Integrated Soft Services', 'text' => 'Housekeeping, pantry, front desk and office support.'],
            ['title' => 'Maintenance Coordination', 'text' => 'Scheduling of electrical, plumbing and minor repair work.'],
            ['title' => 'On-Site Supervision', 'text' => 'Dedicated supervisor with documented checklists.'],
            ['title' => 'Consumables Tracking', 'text' => 'Planned use and replenishment of cleaning and pantry supplies.'],
        ],
        'benefits' => [
            ['title' => 'One Accountable Partner', 'text' => 'Fewer vendors and a clear point of responsibility.'],
            ['title' => 'Consistent Standards', 'text' => 'Checklists and audits keep service quality steady.'],
            ['title' => 'Better Workplace', 'text' => 'Clean, safe and well-run premises for staff and visitors.'],
            ['title' => 'Transparent Reporting', 'text' => 'Periodic reports on attendance, tasks and issues.'],
        ],
        'faqs' => [
            ['q' => 'Can facility management be combined with security?', 'a' => 'Yes. Many clients combine facility management with security services under a single plan and one coordinator.'],
            ['q' => 'Do you provide technicians?', 'a' => 'We coordinate maintenance work and can deploy technicians depending on the scope agreed with you.'],
        ],
    ],
    [
        'title' => 'Housekeeping Services', 'slug' => 'housekeeping-services', 'icon' => 'fa-broom',
        'short' => 'Scheduled cleaning and hygiene support with defined checklists and supervisors for consistently clean workplaces.',
        'intro' => 'Our housekeeping teams keep offices, factories, hospitals, hotels and campuses clean and hygienic with scheduled routines, the right equipment and supervision.',
        'desc' => '<p>We prepare a cleaning schedule for each area — workstations, washrooms, pantries, corridors, shop floors and common areas — and assign trained housekeeping staff to it. Supervisors inspect work against checklists and address feedback promptly.</p><p>Housekeeping can include deep cleaning, floor care, glass cleaning, waste segregation and washroom hygiene. Shifts are planned around your working hours to keep disruption low.</p>',
        'features' => [
            ['title' => 'Daily Cleaning Routines', 'text' => 'Area-wise schedules for desks, floors, washrooms and pantries.'],
            ['title' => 'Deep Cleaning', 'text' => 'Periodic deep cleaning of floors, glass, upholstery and fixtures.'],
            ['title' => 'Washroom Hygiene', 'text' => 'Frequent checks with hygiene logs displayed on site.'],
            ['title' => 'Waste Segregation', 'text' => 'Collection and segregation as per your site guidelines.'],
        ],
        'benefits' => [
            ['title' => 'Healthier Workplace', 'text' => 'Clean surroundings support employee wellbeing.'],
            ['title' => 'Professional Image', 'text' => 'Well-kept premises make a strong impression on visitors.'],
            ['title' => 'Supervised Quality', 'text' => 'Checklists and inspections maintain standards.'],
            ['title' => 'Flexible Shifts', 'text' => 'Cleaning scheduled before, during or after working hours.'],
        ],
        'faqs' => [
            ['q' => 'Do you provide cleaning materials and equipment?', 'a' => 'Materials and equipment can be included or supplied by you — this is agreed in the scope of work.'],
            ['q' => 'Can housekeeping be arranged for night shifts?', 'a' => 'Yes. Housekeeping shifts can be planned at night or outside working hours to avoid disruption.'],
        ],
    ],
    [
        'title' => 'Project Management Services', 'slug' => 'project-management-services', 'icon' => 'fa-diagram-project',
        'short' => 'Planning and coordination of workforce-heavy projects — from manpower scheduling to progress tracking and reporting.',
        'intro' => 'For projects that need large or changing teams, we provide planning and coordination support so the right people are on site at the right time.',
        'desc' => '<p>Workforce-heavy projects — new site set-ups, plant shutdowns, inventory audits or facility transitions — need careful scheduling. We help you plan manpower requirements phase by phase, mobilise teams and coordinate their daily work with your project managers.</p><p>Our coordinators track attendance and progress against the plan and share regular updates, helping you spot delays early and keep the project moving.</p>',
        'features' => [
            ['title' => 'Manpower Planning', 'text' => 'Phase-wise headcount and skill planning for the project.'],
            ['title' => 'Mobilisation', 'text' => 'Quick onboarding and deployment of project teams.'],
            ['title' => 'Daily Coordination', 'text' => 'On-site coordinators working with your project leads.'],
            ['title' => 'Progress Reporting', 'text' => 'Attendance and progress updates against the plan.'],
        ],
        'benefits' => [
            ['title' => 'On-Time Staffing', 'text' => 'The right teams available for each project phase.'],
            ['title' => 'Better Visibility', 'text' => 'Regular reports keep stakeholders informed.'],
            ['title' => 'Reduced Burden', 'text' => 'Your managers focus on delivery, not staffing logistics.'],
            ['title' => 'Controlled Costs', 'text' => 'Headcount aligned to actual project needs.'],
        ],
        'faqs' => [
            ['q' => 'What type of projects do you support?', 'a' => 'We support workforce-heavy projects such as site set-ups, shutdowns, audits, relocations and facility transitions.'],
        ],
    ],
    [
        'title' => 'Payroll Management Services', 'slug' => 'payroll-management-services', 'icon' => 'fa-file-invoice-dollar',
        'short' => 'Attendance-linked salary processing, payslips and payroll records handled through a structured, transparent process.',
        'intro' => 'Our payroll management services take care of the monthly cycle of attendance, salary calculation, payslips and records for your outsourced or contract workforce.',
        'desc' => '<p>Payroll for a large or distributed workforce can be time-consuming and error-prone. We collect attendance inputs, calculate earnings and deductions as per the agreed structure, generate payslips and maintain monthly records.</p><p>The process is documented and shared with your HR or finance team for review, so every payout is traceable. Statutory compliance support is provided as per the applicable scope and regulations.</p>',
        'features' => [
            ['title' => 'Attendance Integration', 'text' => 'Salary calculated from verified attendance data.'],
            ['title' => 'Payslip Generation', 'text' => 'Clear monthly payslips for every employee.'],
            ['title' => 'Payroll Records', 'text' => 'Organised monthly registers and summaries.'],
            ['title' => 'Compliance Support', 'text' => 'Assistance with applicable statutory requirements.'],
        ],
        'benefits' => [
            ['title' => 'Accuracy', 'text' => 'Structured calculations reduce payroll errors.'],
            ['title' => 'Time Saving', 'text' => 'Your HR team spends less time on monthly processing.'],
            ['title' => 'Transparency', 'text' => 'Traceable records for every salary cycle.'],
            ['title' => 'Employee Trust', 'text' => 'Timely, clear payslips build confidence.'],
        ],
        'faqs' => [
            ['q' => 'Can you manage payroll for staff not deployed by you?', 'a' => 'Please discuss your requirement with us — the scope of payroll services is agreed case by case.'],
        ],
    ],
    [
        'title' => 'Labour Supply Services', 'slug' => 'labour-supply-services', 'icon' => 'fa-helmet-safety',
        'short' => 'Reliable labour for loading, construction, production and site work — for short-term or ongoing requirements.',
        'intro' => 'We supply dependable labour teams for physically demanding work such as loading and unloading, construction support, production lines and general site work.',
        'desc' => '<p>Labour requirements often change quickly — a new consignment, a construction phase or a production peak. We maintain a network of workers so teams can be mobilised for your site with a team leader to coordinate the work.</p><p>Workers are briefed on basic safety practices and your site rules before starting. We handle attendance and replacements so the work continues without interruptions.</p>',
        'features' => [
            ['title' => 'Loading & Unloading', 'text' => 'Teams for warehouses, godowns and dispatch areas.'],
            ['title' => 'Construction Support', 'text' => 'Helpers and general labour for site activities.'],
            ['title' => 'Production Helpers', 'text' => 'Support workers for assembly and packing lines.'],
            ['title' => 'Team Leaders', 'text' => 'On-site leads coordinating daily work and attendance.'],
        ],
        'benefits' => [
            ['title' => 'Quick Mobilisation', 'text' => 'Teams arranged for urgent and peak requirements.'],
            ['title' => 'Safety Briefing', 'text' => 'Workers briefed on site rules before starting.'],
            ['title' => 'Steady Output', 'text' => 'Replacements keep work moving without gaps.'],
            ['title' => 'Flexible Engagement', 'text' => 'Daily, weekly, monthly or project-based terms.'],
        ],
        'faqs' => [
            ['q' => 'Can labour be arranged at short notice?', 'a' => 'We try to accommodate urgent requirements. Share the headcount, location and timing and our team will confirm availability.'],
        ],
    ],
    '__process' => $process,
];
