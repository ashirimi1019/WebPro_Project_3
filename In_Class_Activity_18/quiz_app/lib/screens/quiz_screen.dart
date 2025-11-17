import 'package:flutter/material.dart';
import '../models/question.dart';
import '../services/api_service.dart';

class QuizScreen extends StatefulWidget {
  const QuizScreen({super.key});

  @override
  State<QuizScreen> createState() => _QuizScreenState();
}

class _QuizScreenState extends State<QuizScreen> {
  final ApiService _apiService = ApiService();
  List<Question> _questions = [];
  int _currentQuestionIndex = 0;
  int _score = 0;
  bool _isLoading = true;
  String? _selectedAnswer;
  String _feedbackMessage = '';
  bool _isAnswered = false;
  List<String> _shuffledAnswers = [];

  @override
  void initState() {
    super.initState();
    _loadQuestions();
  }

  // Load questions from API
  Future<void> _loadQuestions() async {
    try {
      final questions = await _apiService.fetchQuestions();
      setState(() {
        _questions = questions;
        _isLoading = false;
        if (_questions.isNotEmpty) {
          _shuffledAnswers = _questions[_currentQuestionIndex].getAllAnswers();
        }
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Error loading questions: $e')));
      }
    }
  }

  // Handle answer selection
  void _selectAnswer(String answer) {
    if (_isAnswered) return;

    setState(() {
      _selectedAnswer = answer;
      _isAnswered = true;

      final correctAnswer = _questions[_currentQuestionIndex].correctAnswer;

      if (answer == correctAnswer) {
        _score++;
        _feedbackMessage = 'Correct! The answer is $correctAnswer.';
      } else {
        _feedbackMessage = 'Incorrect. The correct answer is $correctAnswer.';
      }
    });
  }

  // Move to next question
  void _nextQuestion() {
    if (_currentQuestionIndex < _questions.length - 1) {
      setState(() {
        _currentQuestionIndex++;
        _selectedAnswer = null;
        _feedbackMessage = '';
        _isAnswered = false;
        _shuffledAnswers = _questions[_currentQuestionIndex].getAllAnswers();
      });
    } else {
      _showFinalScore();
    }
  }

  // Show final score dialog
  void _showFinalScore() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: const Text('Quiz Complete!'),
        content: Text(
          'Your final score is:\n$_score out of ${_questions.length}',
          style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
          textAlign: TextAlign.center,
        ),
        actions: [
          ElevatedButton(
            onPressed: () {
              Navigator.of(context).pop();
              _restartQuiz();
            },
            child: const Text('Restart Quiz'),
          ),
        ],
      ),
    );
  }

  // Restart the quiz
  void _restartQuiz() {
    setState(() {
      _currentQuestionIndex = 0;
      _score = 0;
      _selectedAnswer = null;
      _feedbackMessage = '';
      _isAnswered = false;
      _isLoading = true;
    });
    _loadQuestions();
  }

  // Get color for answer button
  Color _getButtonColor(String answer) {
    if (!_isAnswered) {
      return Colors.blue;
    }

    final correctAnswer = _questions[_currentQuestionIndex].correctAnswer;

    if (answer == correctAnswer) {
      return Colors.green;
    } else if (answer == _selectedAnswer) {
      return Colors.red;
    }

    return Colors.grey;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Quiz App'),
        backgroundColor: Colors.deepPurple,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _questions.isEmpty
          ? const Center(
              child: Text(
                'No questions available',
                style: TextStyle(fontSize: 18),
              ),
            )
          : Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Question number
                  Text(
                    'Question ${_currentQuestionIndex + 1}/${_questions.length}',
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.deepPurple,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 20),

                  // Score
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.deepPurple.shade50,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      'Score: $_score',
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Colors.deepPurple,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                  const SizedBox(height: 30),

                  // Question text
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(15),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.grey.withValues(alpha: 0.3),
                          spreadRadius: 2,
                          blurRadius: 5,
                          offset: const Offset(0, 3),
                        ),
                      ],
                    ),
                    child: Text(
                      _questions[_currentQuestionIndex].question,
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w500,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                  const SizedBox(height: 30),

                  // Answer options
                  Expanded(
                    child: ListView.builder(
                      itemCount: _shuffledAnswers.length,
                      itemBuilder: (context, index) {
                        final answer = _shuffledAnswers[index];
                        return Padding(
                          padding: const EdgeInsets.symmetric(vertical: 8.0),
                          child: ElevatedButton(
                            onPressed: _isAnswered
                                ? null
                                : () => _selectAnswer(answer),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: _getButtonColor(answer),
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.all(16),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                              elevation: 3,
                            ),
                            child: Text(
                              answer,
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w500,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ),
                        );
                      },
                    ),
                  ),

                  // Feedback message
                  if (_feedbackMessage.isNotEmpty)
                    Container(
                      padding: const EdgeInsets.all(16),
                      margin: const EdgeInsets.symmetric(vertical: 10),
                      decoration: BoxDecoration(
                        color:
                            _selectedAnswer ==
                                _questions[_currentQuestionIndex].correctAnswer
                            ? Colors.green.shade100
                            : Colors.red.shade100,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(
                          color:
                              _selectedAnswer ==
                                  _questions[_currentQuestionIndex]
                                      .correctAnswer
                              ? Colors.green
                              : Colors.red,
                          width: 2,
                        ),
                      ),
                      child: Text(
                        _feedbackMessage,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color:
                              _selectedAnswer ==
                                  _questions[_currentQuestionIndex]
                                      .correctAnswer
                              ? Colors.green.shade900
                              : Colors.red.shade900,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ),

                  // Next button
                  if (_isAnswered)
                    ElevatedButton(
                      onPressed: _nextQuestion,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.deepPurple,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.all(16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                        elevation: 3,
                      ),
                      child: Text(
                        _currentQuestionIndex < _questions.length - 1
                            ? 'Next Question'
                            : 'View Final Score',
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                ],
              ),
            ),
    );
  }
}
