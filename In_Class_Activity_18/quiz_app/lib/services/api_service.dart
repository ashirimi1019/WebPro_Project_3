import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/question.dart';

class ApiService {
  static const String apiUrl =
      'https://opentdb.com/api.php?amount=10&category=9&difficulty=easy&type=multiple';

  // Fetch questions from Open Trivia Database API
  Future<List<Question>> fetchQuestions() async {
    try {
      final response = await http.get(Uri.parse(apiUrl));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final results = data['results'] as List<dynamic>;

        return results.map((json) => Question.fromJson(json)).toList();
      } else {
        throw Exception('Failed to load questions: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Error fetching questions: $e');
    }
  }
}
