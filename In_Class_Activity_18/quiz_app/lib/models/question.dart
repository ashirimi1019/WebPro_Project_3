class Question {
  final String category;
  final String type;
  final String difficulty;
  final String question;
  final String correctAnswer;
  final List<String> incorrectAnswers;

  Question({
    required this.category,
    required this.type,
    required this.difficulty,
    required this.question,
    required this.correctAnswer,
    required this.incorrectAnswers,
  });

  // Factory method to create Question from JSON
  factory Question.fromJson(Map<String, dynamic> json) {
    return Question(
      category: json['category'] ?? '',
      type: json['type'] ?? '',
      difficulty: json['difficulty'] ?? '',
      question: _decodeHtml(json['question'] ?? ''),
      correctAnswer: _decodeHtml(json['correct_answer'] ?? ''),
      incorrectAnswers:
          (json['incorrect_answers'] as List<dynamic>?)
              ?.map((answer) => _decodeHtml(answer.toString()))
              .toList() ??
          [],
    );
  }

  // Helper method to decode HTML entities
  static String _decodeHtml(String text) {
    return text
        .replaceAll('&quot;', '"')
        .replaceAll('&#039;', "'")
        .replaceAll('&amp;', '&')
        .replaceAll('&lt;', '<')
        .replaceAll('&gt;', '>')
        .replaceAll('&ldquo;', '"')
        .replaceAll('&rdquo;', '"')
        .replaceAll('&rsquo;', "'")
        .replaceAll('&lsquo;', "'")
        .replaceAll('&lrm;', '')
        .replaceAll('&rlm;', '')
        .replaceAll('&eacute;', 'é')
        .replaceAll('&ouml;', 'ö')
        .replaceAll('&ntilde;', 'ñ')
        .replaceAll('&uuml;', 'ü')
        .replaceAll('&aacute;', 'á')
        .replaceAll('&iacute;', 'í')
        .replaceAll('&oacute;', 'ó')
        .replaceAll('&uacute;', 'ú')
        .replaceAll('&deg;', '°')
        .replaceAll('&ndash;', '–')
        .replaceAll('&mdash;', '—');
  }

  // Get all answers shuffled
  List<String> getAllAnswers() {
    List<String> allAnswers = [correctAnswer, ...incorrectAnswers];
    allAnswers.shuffle();
    return allAnswers;
  }
}
