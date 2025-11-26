<?php
/**
 * Jeopardy! Battle Arena - Question Bank
 * 
 * Author: Ashir Imran
 * Course: CSC 4370/6370 - Web Programming (Fall 2025)
 * 
 * This file contains all questions organized by category with increasing difficulty.
 * Each category has 5 questions with values: $100, $200, $300, $400, $500
 */

// Main game categories and questions
$categories = [
    // Category 0: General Knowledge
    [
        'name' => 'General Knowledge',
        'questions' => [
            [
                'value' => 100,
                'text' => 'What is the capital city of France?',
                'answer' => 'Paris'
            ],
            [
                'value' => 200,
                'text' => 'How many continents are there on Earth?',
                'answer' => '7'
            ],
            [
                'value' => 300,
                'text' => 'What is the largest ocean on Earth?',
                'answer' => 'Pacific'
            ],
            [
                'value' => 400,
                'text' => 'In what year did World War II end?',
                'answer' => '1945'
            ],
            [
                'value' => 500,
                'text' => 'What is the smallest country in the world by land area?',
                'answer' => 'Vatican City'
            ]
        ]
    ],
    
    // Category 1: Science
    [
        'name' => 'Science',
        'questions' => [
            [
                'value' => 100,
                'text' => 'What planet is known as the Red Planet?',
                'answer' => 'Mars'
            ],
            [
                'value' => 200,
                'text' => 'What is the chemical symbol for gold?',
                'answer' => 'Au'
            ],
            [
                'value' => 300,
                'text' => 'How many bones are in the adult human body?',
                'answer' => '206'
            ],
            [
                'value' => 400,
                'text' => 'What is the speed of light in meters per second? (Use scientific notation: 3e8)',
                'answer' => '3e8'
            ],
            [
                'value' => 500,
                'text' => 'What is the powerhouse of the cell?',
                'answer' => 'Mitochondria'
            ]
        ]
    ],
    
    // Category 2: History
    [
        'name' => 'History',
        'questions' => [
            [
                'value' => 100,
                'text' => 'Who was the first President of the United States?',
                'answer' => 'George Washington'
            ],
            [
                'value' => 200,
                'text' => 'In what year did Christopher Columbus reach the Americas?',
                'answer' => '1492'
            ],
            [
                'value' => 300,
                'text' => 'What ancient wonder was located in Alexandria, Egypt?',
                'answer' => 'Lighthouse'
            ],
            [
                'value' => 400,
                'text' => 'Who was the first person to walk on the moon?',
                'answer' => 'Neil Armstrong'
            ],
            [
                'value' => 500,
                'text' => 'What year did the Berlin Wall fall?',
                'answer' => '1989'
            ]
        ]
    ],
    
    // Category 3: Movies & TV
    [
        'name' => 'Movies & TV',
        'questions' => [
            [
                'value' => 100,
                'text' => 'What movie features a character named Luke Skywalker?',
                'answer' => 'Star Wars'
            ],
            [
                'value' => 200,
                'text' => 'Who directed the movie "Titanic"?',
                'answer' => 'James Cameron'
            ],
            [
                'value' => 300,
                'text' => 'What is the name of the wizarding school in Harry Potter?',
                'answer' => 'Hogwarts'
            ],
            [
                'value' => 400,
                'text' => 'Which movie won the Oscar for Best Picture in 1994, featuring a man sitting on a park bench?',
                'answer' => 'Forrest Gump'
            ],
            [
                'value' => 500,
                'text' => 'What TV series features characters named Ross, Rachel, and Chandler?',
                'answer' => 'Friends'
            ]
        ]
    ],
    
    // Category 4: Technology
    [
        'name' => 'Technology',
        'questions' => [
            [
                'value' => 100,
                'text' => 'What does "WWW" stand for in a website address?',
                'answer' => 'World Wide Web'
            ],
            [
                'value' => 200,
                'text' => 'What company created the iPhone?',
                'answer' => 'Apple'
            ],
            [
                'value' => 300,
                'text' => 'What programming language is primarily used for web page styling?',
                'answer' => 'CSS'
            ],
            [
                'value' => 400,
                'text' => 'What does "CPU" stand for in computer terms?',
                'answer' => 'Central Processing Unit'
            ],
            [
                'value' => 500,
                'text' => 'Who is known as the father of computer science and artificial intelligence?',
                'answer' => 'Alan Turing'
            ]
        ]
    ]
];

// Final Jeopardy question
$finalJeopardy = [
    'category' => 'World Geography',
    'text' => 'This mountain range stretches over 4,500 miles along the western coast of South America and is the longest continental mountain range in the world.',
    'answer' => 'Andes'
];

?>
